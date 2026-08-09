<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageOptimizationService
{
    /**
     * Maximum width for full-size images (pixels).
     */
    public const MAX_WIDTH = 1920;

    /**
     * Maximum height for full-size images (pixels).
     */
    public const MAX_HEIGHT = 1080;

    /**
     * Maximum width for thumbnail images (pixels).
     */
    public const THUMBNAIL_WIDTH = 300;

    /**
     * Maximum height for thumbnail images (pixels).
     */
    public const THUMBNAIL_HEIGHT = 300;

    /**
     * JPEG compression quality (1-100).
     */
    public const JPEG_QUALITY = 82;

    /**
     * WebP compression quality (1-100).
     */
    public const WEBP_QUALITY = 80;

    /**
     * Check if GD extension is available.
     */
    public function isGdAvailable(): bool
    {
        return extension_loaded('gd');
    }

    /**
     * Check if a file is a supported image type.
     */
    public function isSupportedImage(UploadedFile $file): bool
    {
        $mimeType = $file->getMimeType();

        return in_array($mimeType, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'], true);
    }

    /**
     * Optimize an uploaded image file.
     *
     * Returns an array with paths to the optimized full image and thumbnail.
     * Original is kept as backup if optimization fails.
     *
     * @return array{full: string, thumbnail: string, original_size: int, optimized_size: int}
     */
    public function optimize(UploadedFile $file, string $directory = 'media'): array
    {
        if (! $this->isGdAvailable() || ! $this->isSupportedImage($file)) {
            return $this->storeOriginal($file, $directory);
        }

        $originalSize = $file->getSize();
        $imageInfo = @getimagesize($file->getRealPath());

        if ($imageInfo === false) {
            return $this->storeOriginal($file, $directory);
        }

        [$width, $height, $type] = $imageInfo;

        try {
            $sourceImage = $this->createImageFromUpload($file, $type, $width, $height);

            if ($sourceImage === false) {
                return $this->storeOriginal($file, $directory);
            }

            // Create full-size optimized image
            $fullPath = $this->createResizedImage(
                $sourceImage,
                $type,
                $width,
                $height,
                self::MAX_WIDTH,
                self::MAX_HEIGHT,
                $directory,
                'full_'
            );

            // Create thumbnail
            $thumbPath = $this->createResizedImage(
                $sourceImage,
                $type,
                $width,
                $height,
                self::THUMBNAIL_WIDTH,
                self::THUMBNAIL_HEIGHT,
                $directory,
                'thumb_'
            );

            imagedestroy($sourceImage);

            // Get optimized file size
            $optimizedSize = Storage::disk('public')->exists($fullPath)
                ? Storage::disk('public')->size($fullPath)
                : $originalSize;

            return [
                'full' => $fullPath,
                'thumbnail' => $thumbPath,
                'original_size' => $originalSize,
                'optimized_size' => $optimizedSize,
            ];
        } catch (\Throwable) {
            // Fallback: store original file
            return $this->storeOriginal($file, $directory);
        }
    }

    /**
     * Generate a thumbnail URL from a given image path.
     */
    public function getThumbnailPath(string $fullImagePath): string
    {
        $pathInfo = pathinfo($fullImagePath);

        return $pathInfo['dirname'].'/thumb_'.$pathInfo['basename'];
    }

    /**
     * Calculate optimization savings percentage.
     */
    public function calculateSavings(int $originalSize, int $optimizedSize): float
    {
        if ($originalSize <= 0) {
            return 0;
        }

        return round((($originalSize - $optimizedSize) / $originalSize) * 100, 1);
    }

    /**
     * Get supported image MIME types.
     *
     * @return list<string>
     */
    public function getSupportedMimeTypes(): array
    {
        return ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    }

    /**
     * Store the original file without optimization (fallback).
     *
     * @return array{full: string, thumbnail: string, original_size: int, optimized_size: int}
     */
    private function storeOriginal(UploadedFile $file, string $directory): array
    {
        $filePath = $file->store($directory, 'public');
        $fileSize = $file->getSize();

        return [
            'full' => $filePath,
            'thumbnail' => $filePath,
            'original_size' => $fileSize,
            'optimized_size' => $fileSize,
        ];
    }

    /**
     * Create a GD image resource from an uploaded file.
     *
     * @return resource|false
     */
    private function createImageFromUpload(UploadedFile $file, int $type, int $width, int $height)
    {
        return match ($type) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($file->getRealPath()),
            IMAGETYPE_PNG => imagecreatefrompng($file->getRealPath()),
            IMAGETYPE_GIF => imagecreatefromgif($file->getRealPath()),
            IMAGETYPE_WEBP => imagecreatefromwebp($file->getRealPath()),
            default => false,
        };
    }

    /**
     * Create a resized image and store it.
     *
     * @param  resource  $sourceImage
     */
    private function createResizedImage(
        $sourceImage,
        int $sourceType,
        int $sourceWidth,
        int $sourceHeight,
        int $maxWidth,
        int $maxHeight,
        string $directory,
        string $prefix,
    ): string {
        // Calculate new dimensions maintaining aspect ratio
        $dimensions = $this->calculateDimensions($sourceWidth, $sourceHeight, $maxWidth, $maxHeight);

        $resizedImage = imagecreatetruecolor($dimensions['width'], $dimensions['height']);

        // Preserve transparency for PNG and GIF
        if ($sourceType === IMAGETYPE_PNG || $sourceType === IMAGETYPE_GIF) {
            imagealphablending($resizedImage, false);
            imagesavealpha($resizedImage, true);
            $transparent = imagecolorallocatealpha($resizedImage, 0, 0, 0, 127);
            imagefilledrectangle($resizedImage, 0, 0, $dimensions['width'], $dimensions['height'], $transparent);
        }

        imagecopyresampled(
            $resizedImage,
            $sourceImage,
            0, 0, 0, 0,
            $dimensions['width'],
            $dimensions['height'],
            $sourceWidth,
            $sourceHeight,
        );

        // Generate filename
        $filename = $prefix.Str::random(16).'.'.$this->getExtensionFromType($sourceType);
        $fullPath = $directory.'/'.$filename;

        // Store to disk
        $tmpPath = storage_path('app/'.$filename);
        $this->saveImageToDisk($resizedImage, $sourceType, $tmpPath);

        $contents = file_get_contents($tmpPath);
        Storage::disk('public')->put($fullPath, $contents);

        @unlink($tmpPath);
        imagedestroy($resizedImage);

        return $fullPath;
    }

    /**
     * Calculate dimensions maintaining aspect ratio.
     *
     * @return array{width: int, height: int}
     */
    private function calculateDimensions(int $originalWidth, int $originalHeight, int $maxWidth, int $maxHeight): array
    {
        if ($originalWidth <= $maxWidth && $originalHeight <= $maxHeight) {
            return ['width' => $originalWidth, 'height' => $originalHeight];
        }

        $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);

        return [
            'width' => (int) round($originalWidth * $ratio),
            'height' => (int) round($originalHeight * $ratio),
        ];
    }

    /**
     * Save image resource to disk with appropriate format and quality.
     *
     * @param  resource  $image
     */
    private function saveImageToDisk($image, int $type, string $path): void
    {
        match ($type) {
            IMAGETYPE_JPEG => imagejpeg($image, $path, self::JPEG_QUALITY),
            IMAGETYPE_PNG => imagepng($image, $path, 6), // compression level 6
            IMAGETYPE_GIF => imagegif($image, $path),
            IMAGETYPE_WEBP => imagewebp($image, $path, self::WEBP_QUALITY),
        };
    }

    /**
     * Get file extension from GD image type constant.
     */
    private function getExtensionFromType(int $type): string
    {
        return match ($type) {
            IMAGETYPE_JPEG => 'jpg',
            IMAGETYPE_PNG => 'png',
            IMAGETYPE_GIF => 'gif',
            IMAGETYPE_WEBP => 'webp',
            default => 'jpg',
        };
    }
}
