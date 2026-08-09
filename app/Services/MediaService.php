<?php

namespace App\Services;

use App\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

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
     */
    public function upload(
        UploadedFile $file,
        Model $mediable,
        ?string $altText = null,
    ): Media {
        $fileType = $this->determineFileType($file);
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
            $filePath = $file->store('media', 'public');
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
     * Upload multiple files and create Media records.
     *
     * @return Collection<int, Media>
     */
    public function uploadMultiple(
        array $files,
        Model $mediable,
    ): Collection {
        $media = collect();

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $media->push($this->upload($file, $mediable));
            }
        }

        return $media;
    }

    /**
     * Delete a media record and its file from storage.
     */
    public function delete(Media $media): bool
    {
        if ($media->file_path && Storage::disk('public')->exists($media->file_path)) {
            Storage::disk('public')->delete($media->file_path);
        }

        return $media->delete();
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
