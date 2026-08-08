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

    /**
     * Upload a file and create a Media record.
     */
    public function upload(
        UploadedFile $file,
        Model $mediable,
        ?string $altText = null,
    ): Media {
        $filePath = $file->store('media', 'public');
        $fileType = $this->determineFileType($file);

        return Media::create([
            'mediable_type' => get_class($mediable),
            'mediable_id' => $mediable->id,
            'file_path' => $filePath,
            'file_name' => $file->getClientOriginalName(),
            'file_type' => $fileType,
            'file_size' => $file->getSize(),
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
