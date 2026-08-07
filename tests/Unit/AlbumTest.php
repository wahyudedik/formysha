<?php

use App\Models\Album;
use App\Models\Child;
use App\Models\Media;

it('can create an album', function () {
    $album = Album::factory()->create();

    expect($album)->toBeInstanceOf(Album::class);
    expect($album->name)->toBeString();
    expect($album->child_id)->toBeInt();
});

it('belongs to a child', function () {
    $album = Album::factory()->create();

    expect($album->child)->not->toBeNull();
    expect($album->child)->toBeInstanceOf(Child::class);
});

it('has many media', function () {
    $album = Album::factory()->create();

    $media = Media::factory()->create([
        'mediable_type' => Album::class,
        'mediable_id' => $album->id,
    ]);

    expect($album->media)->toHaveCount(1);
    expect($album->media->first())->toBeInstanceOf(Media::class);
});

it('returns correct media count', function () {
    $album = Album::factory()->create();
    Media::factory()->create([
        'mediable_type' => Album::class,
        'mediable_id' => $album->id,
    ]);
    Media::factory()->create([
        'mediable_type' => Album::class,
        'mediable_id' => $album->id,
    ]);

    expect($album->media_count)->toBe(2);
});

it('has correct fillable attributes', function () {
    $album = new Album;

    expect($album->getFillable())->toBe([
        'child_id',
        'name',
        'description',
        'cover_photo',
        'is_private',
        'sort_order',
    ]);
});

it('casts is_private as boolean', function () {
    $album = Album::factory()->create(['is_private' => true]);

    expect($album->is_private)->toBeTrue();
});

it('casts sort_order as integer', function () {
    $album = Album::factory()->create(['sort_order' => 5]);

    expect($album->sort_order)->toBeInt();
    expect($album->sort_order)->toBe(5);
});

it('can be created with public state', function () {
    $album = Album::factory()->public()->create();

    expect($album->is_private)->toBeFalse();
});

it('can be created with private state', function () {
    $album = Album::factory()->private()->create();

    expect($album->is_private)->toBeTrue();
});

it('can be created with cover photo state', function () {
    $album = Album::factory()->withCover()->create();

    expect($album->cover_photo)->not->toBeNull();
    expect($album->cover_photo)->toBeString();
});

it('child has many albums', function () {
    $child = Child::factory()->create();
    Album::factory()->create(['child_id' => $child->id]);
    Album::factory()->create(['child_id' => $child->id]);

    expect($child->albums)->toHaveCount(2);
});
