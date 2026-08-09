<?php

use App\Services\ImageOptimizationService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

describe('ImageOptimizationService', function () {
    beforeEach(function () {
        Storage::fake('public');
        $this->service = new ImageOptimizationService;
    });

    it('checks GD availability', function () {
        $result = $this->service->isGdAvailable();

        expect($result)->toBeBool();
    });

    it('identifies supported image types', function () {
        $jpg = UploadedFile::fake()->image('photo.jpg', 100, 100);
        $png = UploadedFile::fake()->create('photo.png', 100, 'image/png');
        $txt = UploadedFile::fake()->create('file.txt', 100, 'text/plain');

        expect($this->service->isSupportedImage($jpg))->toBeTrue();
        expect($this->service->isSupportedImage($png))->toBeTrue();
        expect($this->service->isSupportedImage($txt))->toBeFalse();
    });

    it('returns supported MIME types list', function () {
        $types = $this->service->getSupportedMimeTypes();

        expect($types)->toBeArray()
            ->toContain('image/jpeg')
            ->toContain('image/png')
            ->toContain('image/gif')
            ->toContain('image/webp');
    });

    it('calculates savings percentage correctly', function () {
        expect($this->service->calculateSavings(1000, 500))->toBe(50.0);
        expect($this->service->calculateSavings(1000, 800))->toBe(20.0);
        expect($this->service->calculateSavings(1000, 1000))->toBe(0.0);
        expect($this->service->calculateSavings(0, 0))->toBe(0.0);
    });

    it('generates correct thumbnail path from full path', function () {
        $fullPath = 'media/abc123.jpg';
        $thumbPath = $this->service->getThumbnailPath($fullPath);

        expect($thumbPath)->toBe('media/thumb_abc123.jpg');
    });

    it('handles non-image file upload by storing original', function () {
        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $result = $this->service->optimize($file, 'media');

        expect($result)->toHaveKeys(['full', 'thumbnail', 'original_size', 'optimized_size']);
        expect($result['original_size'])->toBeGreaterThan(0);
    });

    it('stores image with correct structure even when GD fails', function () {
        // Create a minimal valid JPEG-like file that GD can't process
        $file = UploadedFile::fake()->create('test.bin', 100, 'application/octet-stream');

        $result = $this->service->optimize($file, 'media');

        expect($result)->toHaveKeys(['full', 'thumbnail', 'original_size', 'optimized_size']);
        expect($result['full'])->not->toBeEmpty();
    });

    it('returns correct constants', function () {
        expect(ImageOptimizationService::MAX_WIDTH)->toBe(1920);
        expect(ImageOptimizationService::MAX_HEIGHT)->toBe(1080);
        expect(ImageOptimizationService::THUMBNAIL_WIDTH)->toBe(300);
        expect(ImageOptimizationService::THUMBNAIL_HEIGHT)->toBe(300);
        expect(ImageOptimizationService::JPEG_QUALITY)->toBe(82);
        expect(ImageOptimizationService::WEBP_QUALITY)->toBe(80);
    });
});
