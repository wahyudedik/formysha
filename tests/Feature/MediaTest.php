<?php

use App\Models\Album;
use App\Models\Child;
use App\Models\Diary;
use App\Models\Media;
use App\Models\Timeline;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

it('requires authentication to upload media for timeline', function () {
    $child = Child::factory()->create();
    $timeline = Timeline::factory()->create(['child_id' => $child->id]);

    $this->post(route('media.store.timeline', [$child, $timeline]), [
        'media' => [UploadedFile::fake()->image('test.jpg')],
    ])->assertRedirect(route('login'));
});

it('uploads media for timeline', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    $timeline = Timeline::factory()->create(['child_id' => $child->id, 'user_id' => $user->id]);

    $file = UploadedFile::fake()->image('moment.jpg', 800, 600)->size(1024);

    actingAs($user)
        ->post(route('media.store.timeline', [$child, $timeline]), [
            'media' => [$file],
        ])
        ->assertRedirect(route('timeline.show', [$child, $timeline]));

    assertDatabaseHas('media', [
        'mediable_type' => Timeline::class,
        'mediable_id' => $timeline->id,
        'file_name' => 'moment.jpg',
        'file_type' => 'photo',
    ]);
});

it('prevents other users from uploading media to timeline', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    $timeline = Timeline::factory()->create(['child_id' => $child->id, 'user_id' => $user->id]);

    $file = UploadedFile::fake()->image('hack.jpg', 800, 600)->size(1024);

    actingAs($otherUser)
        ->post(route('media.store.timeline', [$child, $timeline]), [
            'media' => [$file],
        ])
        ->assertForbidden();
});

it('validates media is required when uploading to timeline', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    $timeline = Timeline::factory()->create(['child_id' => $child->id, 'user_id' => $user->id]);

    actingAs($user)
        ->post(route('media.store.timeline', [$child, $timeline]), [])
        ->assertSessionHasErrors(['media']);
});

it('uploads media for album', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    $album = Album::factory()->create(['child_id' => $child->id]);

    $file = UploadedFile::fake()->image('album-photo.jpg', 800, 600)->size(1024);

    actingAs($user)
        ->post(route('media.store.album', [$child, $album]), [
            'media' => [$file],
        ])
        ->assertRedirect(route('albums.show', [$child, $album]));

    assertDatabaseHas('media', [
        'mediable_type' => Album::class,
        'mediable_id' => $album->id,
        'file_name' => 'album-photo.jpg',
        'file_type' => 'photo',
    ]);
});

it('prevents other users from uploading media to album', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    $album = Album::factory()->create(['child_id' => $child->id]);

    $file = UploadedFile::fake()->image('hack.jpg', 800, 600)->size(1024);

    actingAs($otherUser)
        ->post(route('media.store.album', [$child, $album]), [
            'media' => [$file],
        ])
        ->assertForbidden();
});

it('uploads media for diary', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    $diary = Diary::factory()->create(['child_id' => $child->id, 'user_id' => $user->id]);

    $file = UploadedFile::fake()->image('diary-photo.jpg', 800, 600)->size(1024);

    actingAs($user)
        ->post(route('media.store.diary', [$child, $diary]), [
            'media' => [$file],
        ])
        ->assertRedirect(route('diaries.show', [$child, $diary]));

    assertDatabaseHas('media', [
        'mediable_type' => Diary::class,
        'mediable_id' => $diary->id,
        'file_name' => 'diary-photo.jpg',
        'file_type' => 'photo',
    ]);
});

it('prevents other users from uploading media to diary', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    $diary = Diary::factory()->create(['child_id' => $child->id, 'user_id' => $user->id]);

    $file = UploadedFile::fake()->image('hack.jpg', 800, 600)->size(1024);

    actingAs($otherUser)
        ->post(route('media.store.diary', [$child, $diary]), [
            'media' => [$file],
        ])
        ->assertForbidden();
});

it('deletes media record', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    $media = Media::factory()->photo()->create([
        'mediable_type' => Child::class,
        'mediable_id' => $child->id,
    ]);

    actingAs($user)
        ->delete(route('media.destroy', [$child, $media]))
        ->assertRedirect();

    $this->assertDatabaseMissing('media', ['id' => $media->id]);
});

it('prevents other users from deleting media', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    $media = Media::factory()->photo()->create([
        'mediable_type' => Child::class,
        'mediable_id' => $child->id,
    ]);

    actingAs($otherUser)
        ->delete(route('media.destroy', [$child, $media]))
        ->assertForbidden();
});
