<?php

use App\Models\Child;
use App\Models\Media;
use App\Models\Timeline;

it('can create a media', function () {
    $media = Media::factory()->create();

    $this->assertNotNull($media);
    $this->assertDatabaseHas('media', ['id' => $media->id]);
});

it('belongs to a mediable model (polymorphic)', function () {
    $child = Child::factory()->create();
    $media = Media::factory()->create([
        'mediable_type' => Child::class,
        'mediable_id' => $child->id,
    ]);

    $this->assertInstanceOf(Child::class, $media->mediable);
    $this->assertEquals($child->id, $media->mediable_id);
});

it('belongs to a timeline via polymorphic', function () {
    $timeline = Timeline::factory()->create();
    $media = Media::factory()->create([
        'mediable_type' => Timeline::class,
        'mediable_id' => $timeline->id,
    ]);

    $this->assertInstanceOf(Timeline::class, $media->mediable);
    $this->assertEquals($timeline->id, $media->mediable_id);
});

it('returns correct file type labels', function () {
    $media = Media::factory()->photo()->create();
    $this->assertEquals('📷 Foto', $media->file_type_label);

    $media = Media::factory()->video()->create();
    $this->assertEquals('🎬 Video', $media->file_type_label);

    $media = Media::factory()->audio()->create();
    $this->assertEquals('🎵 Audio', $media->file_type_label);

    $media = Media::factory()->document()->create();
    $this->assertEquals('📄 Dokumen', $media->file_type_label);
});

it('returns formatted file size', function () {
    $media = Media::factory()->create(['file_size' => 1024]);
    $this->assertEquals('1 KB', $media->formatted_size);

    $media = Media::factory()->create(['file_size' => 1_048_576]);
    $this->assertEquals('1 MB', $media->formatted_size);

    $media = Media::factory()->create(['file_size' => 1_073_741_824]);
    $this->assertEquals('1 GB', $media->formatted_size);

    $media = Media::factory()->create(['file_size' => 500]);
    $this->assertEquals('500 B', $media->formatted_size);
});

it('can create photo state', function () {
    $media = Media::factory()->photo()->create();
    $this->assertEquals('photo', $media->file_type);
    $this->assertNotNull($media->alt_text);
});

it('can create video state', function () {
    $media = Media::factory()->video()->create();
    $this->assertEquals('video', $media->file_type);
});

it('can create audio state', function () {
    $media = Media::factory()->audio()->create();
    $this->assertEquals('audio', $media->file_type);
});

it('can create document state', function () {
    $media = Media::factory()->document()->create();
    $this->assertEquals('document', $media->file_type);
});

it('casts file_size and sort_order as integer', function () {
    $media = Media::factory()->create([
        'file_size' => 5000,
        'sort_order' => 3,
    ]);

    $this->assertIsInt($media->file_size);
    $this->assertIsInt($media->sort_order);
    $this->assertEquals(5000, $media->file_size);
    $this->assertEquals(3, $media->sort_order);
});

it('child can have many media via morphMany', function () {
    $child = Child::factory()->create();
    Media::factory()->count(3)->create([
        'mediable_type' => Child::class,
        'mediable_id' => $child->id,
    ]);

    $this->assertCount(3, $child->media);
});
