<?php

namespace App\Services;

use App\Models\Child;
use App\Models\Media;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class MediaService
{
    /**
     * Allowed MIME types for media upload.
     */
    public const ALLOWED_TYPES = [
        'image' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
        'video' => ['video/mp4', 'video/quicktime', 'video/webm', 'video/x-msvideo'],
        'audio' => ['audio/mpeg', 'audio/wav', 'audio/ogg', 'audio/mp3'],
    ];

    /**
     * Maximum file size in bytes (10MB).
     */
    public const MAX_FILE_SIZE = 10_485_760;

    public function __construct(
        private ?ImageOptimizationService $imageOptimizer = null,
    ) {
        $this->imageOptimizer = $imageOptimizer ?? new ImageOptimizationService;
    }

    /**
     * Upload a file and create a Media record.
     *
     * Plan limits (max_photos, max_videos, max_storage_mb) are enforced
     * BEFORE the file is written to storage.
     */
    public function upload(
        UploadedFile $file,
        Model $mediable,
        ?string $altText = null,
    ): Media {
        $fileType = $this->determineFileType($file);

        $this->enforceUploadLimits($mediable, [$file]);

        return $this->storeFile($file, $mediable, $fileType, $altText);
    }

    /**
     * Upload multiple files and create Media records.
     *
     * The whole batch is validated against plan limits before any file
     * is written to storage, so a rejected batch stores nothing.
     *
     * @return Collection<int, Media>
     */
    public function uploadMultiple(
        array $files,
        Model $mediable,
    ): Collection {
        $validFiles = array_values(array_filter(
            $files,
            fn ($file) => $file instanceof UploadedFile
        ));

        if ($validFiles !== []) {
            $this->enforceUploadLimits($mediable, $validFiles);
        }

        $media = collect();

        foreach ($validFiles as $file) {
            $media->push($this->storeFile($file, $mediable, $this->determineFileType($file)));
        }

        return $media;
    }

    /**
     * Delete a media record and its file from storage.
     */
    public function delete(Media $media): bool
    {
        // Delete from private disk + any legacy public copy from before the
        // private storage migration.
        if ($media->file_path) {
            Storage::disk('private')->delete($media->file_path);
            Storage::disk('public')->delete($media->file_path);
        }

        if ($media->thumbnail_path) {
            Storage::disk('private')->delete($media->thumbnail_path);
            Storage::disk('public')->delete($media->thumbnail_path);
        }

        return $media->delete();
    }

    /**
     * Store the uploaded file (optimize when possible) and create the record.
     */
    private function storeFile(
        UploadedFile $file,
        Model $mediable,
        string $fileType,
        ?string $altText = null,
    ): Media {
        $originalSize = $file->getSize();
        $filePath = null;
        $thumbnailPath = null;
        $optimizedSize = null;

        // Auto-optimize images if GD is available
        if ($fileType === 'photo' && $this->imageOptimizer->isGdAvailable() && $this->imageOptimizer->isSupportedImage($file)) {
            $result = $this->imageOptimizer->optimize($file, 'media');
            $filePath = $result['full'];
            $thumbnailPath = $result['thumbnail'];
            $optimizedSize = $result['optimized_size'];
        } else {
            $filePath = $file->store('media', 'private');
        }

        return Media::create([
            'mediable_type' => get_class($mediable),
            'mediable_id' => $mediable->id,
            'file_path' => $filePath,
            'file_name' => $file->getClientOriginalName(),
            'file_type' => $fileType,
            'file_size' => $originalSize,
            'thumbnail_path' => $thumbnailPath,
            'optimized_size' => $optimizedSize,
            'alt_text' => $altText,
        ]);
    }

    /**
     * Enforce the owning tenant's plan limits for the given incoming files.
     *
     * Runs BEFORE any storage write. Throws a ValidationException (which
     * becomes a redirect-back with errors on web and JSON 422 on API)
     * when a limit would be exceeded.
     *
     * @param  array<int, UploadedFile>  $files
     */
    private function enforceUploadLimits(Model $mediable, array $files): void
    {
        $tenant = $this->resolveTenant($mediable);

        if (! $tenant) {
            return;
        }

        $plan = $tenant->activeSubscription?->plan;

        // Without an active plan there are no configured limits to enforce.
        if (! $plan) {
            return;
        }

        $newPhotos = 0;
        $newVideos = 0;
        $newBytes = 0;

        foreach ($files as $file) {
            $fileType = $this->determineFileType($file);

            if ($fileType === 'photo') {
                $newPhotos++;
            } elseif ($fileType === 'video') {
                $newVideos++;
            }

            $newBytes += (int) $file->getSize();
        }

        if ($newPhotos > 0 && ! $tenant->canUploadAdditionalPhotos($newPhotos)) {
            throw ValidationException::withMessages([
                'media' => __('limits.photos'),
            ]);
        }

        if ($newVideos > 0 && ! $tenant->canUploadAdditionalVideos($newVideos)) {
            throw ValidationException::withMessages([
                'media' => __('limits.videos'),
            ]);
        }

        if ($newBytes > 0 && ! $tenant->canUploadStorage($newBytes)) {
            throw ValidationException::withMessages([
                'media' => __('limits.storage'),
            ]);
        }
    }

    /**
     * Resolve the tenant that owns the media target (child or its parent
     * timeline / album / diary).
     */
    private function resolveTenant(Model $mediable): ?Tenant
    {
        /** @var Child|Model $child */
        $child = $mediable instanceof Child
            ? $mediable
            : ($mediable->child ?? null);

        if (! $child instanceof Child) {
            return null;
        }

        return $child->tenant;
    }

    /**
     * Determine the file type category from the uploaded file.
     */
    private function determineFileType(UploadedFile $file): string
    {
        $mimeType = $file->getMimeType();

        foreach (self::ALLOWED_TYPES as $type => $mimeTypes) {
            if (in_array($mimeType, $mimeTypes, true)) {
                return $type === 'image' ? 'photo' : $type;
            }
        }

        return 'document';
    }
}
