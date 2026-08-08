<?php

use App\Models\Album;
use App\Models\Child;
use App\Models\Diary;
use App\Models\Media;
use App\Models\Timeline;
use App\Services\MediaService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

describe('MediaService', function () {
    it('uploads a single file and creates media record', function () {
        Storage::fake('public');

        $child = Child::factory()->create();
        $service = new MediaService;
        $file = UploadedFile::fake()->image('photo.jpg', 800, 600)->size(1024);

        $media = $service->upload($file, $child);

        expect($media)->toBeInstanceOf(Media::class)
            ->and($media->mediable_type)->toBe(Child::class)
            ->and($media->mediable_id)->toBe($child->id)
            ->and($media->file_name)->toBe('photo.jpg')
            ->and($media->file_type)->toBe('photo')
            ->and($media->file_size)->toBeGreaterThan(0);

        Storage::disk('public')->assertExists($media->file_path);
    });

    it('uploads multiple files and creates media records', function () {
        Storage::fake('public');

        $child = Child::factory()->create();
        $service = new MediaService;

        $files = [
            UploadedFile::fake()->image('photo1.jpg', 800, 600)->size(1024),
            UploadedFile::fake()->image('photo2.png', 800, 600)->size(2048),
        ];

        $mediaCollection = $service->uploadMultiple($files, $child);

        expect($mediaCollection->count())->toBe(2);

        foreach ($mediaCollection as $media) {
            expect($media)->toBeInstanceOf(Media::class)
                ->and($media->mediable_id)->toBe($child->id);
        }
    });

    it('determines video file type correctly', function () {
        Storage::fake('public');

        $child = Child::factory()->create();
        $service = new MediaService;
        $file = UploadedFile::fake()->create('video.mp4', 5120, 'video/mp4');

        $media = $service->upload($file, $child);

        expect($media->file_type)->toBe('video');
    });

    it('determines audio file type correctly', function () {
        Storage::fake('public');

        $child = Child::factory()->create();
        $service = new MediaService;
        $file = UploadedFile::fake()->create('audio.mp3', 3072, 'audio/mpeg');

        $media = $service->upload($file, $child);

        expect($media->file_type)->toBe('audio');
    });

    it('stores media for timeline', function () {
        Storage::fake('public');

        $child = Child::factory()->create();
        $timeline = Timeline::factory()->create(['child_id' => $child->id]);
        $service = new MediaService;
        $file = UploadedFile::fake()->image('moment.jpg', 800, 600)->size(1024);

        $media = $service->upload($file, $timeline);

        expect($media->mediable_type)->toBe(Timeline::class)
            ->and($media->mediable_id)->toBe($timeline->id);
    });

    it('stores media for album', function () {
        Storage::fake('public');

        $child = Child::factory()->create();
        $album = Album::factory()->create(['child_id' => $child->id]);
        $service = new MediaService;
        $file = UploadedFile::fake()->image('album-photo.jpg', 800, 600)->size(1024);

        $media = $service->upload($file, $album);

        expect($media->mediable_type)->toBe(Album::class)
            ->and($media->mediable_id)->toBe($album->id);
    });

    it('stores media for diary', function () {
        Storage::fake('public');

        $child = Child::factory()->create();
        $diary = Diary::factory()->create(['child_id' => $child->id, 'user_id' => $child->user_id]);
        $service = new MediaService;
        $file = UploadedFile::fake()->image('diary-photo.jpg', 800, 600)->size(1024);

        $media = $service->upload($file, $diary);

        expect($media->mediable_type)->toBe(Diary::class)
            ->and($media->mediable_id)->toBe($diary->id);
    });

    it('deletes media record and file from storage', function () {
        Storage::fake('public');

        $child = Child::factory()->create();
        $service = new MediaService;
        $file = UploadedFile::fake()->image('to-delete.jpg', 800, 600)->size(1024);

        $media = $service->upload($file, $child);
        $filePath = $media->file_path;

        $result = $service->delete($media);

        expect($result)->toBeTrue();
        expect(Media::find($media->id))->toBeNull();
        Storage::disk('public')->assertMissing($filePath);
    });

    it('saves alt text when provided', function () {
        Storage::fake('public');

        $child = Child::factory()->create();
        $service = new MediaService;
        $file = UploadedFile::fake()->image('captioned.jpg', 800, 600)->size(1024);

        $media = $service->upload($file, $child, 'Foto ulang tahun');

        expect($media->alt_text)->toBe('Foto ulang tahun');
    });

    it('skips non-uploaded-file items in uploadMultiple', function () {
        Storage::fake('public');

        $child = Child::factory()->create();
        $service = new MediaService;

        $files = [
            UploadedFile::fake()->image('valid.jpg', 800, 600)->size(1024),
            'not-a-file',
        ];

        $mediaCollection = $service->uploadMultiple($files, $child);

        expect($mediaCollection->count())->toBe(1);
    });
});
