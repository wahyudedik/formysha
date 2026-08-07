<?php

use App\Models\Album;
use App\Models\Child;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

it('requires authentication to access album index', function () {
    $child = Child::factory()->create();

    $this->get(route('albums.index', $child))
        ->assertRedirect(route('login'));
});

it('shows empty state when no albums exist', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->get(route('albums.index', $child))
        ->assertOk()
        ->assertSee('Belum Ada Album');
});

it('lists albums for a child', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    Album::factory()->create([
        'child_id' => $child->id,
        'name' => 'Momen Keluarga',
    ]);

    actingAs($user)
        ->get(route('albums.index', $child))
        ->assertOk()
        ->assertSee('Momen Keluarga');
});

it('prevents other users from viewing albums', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    actingAs($otherUser)
        ->get(route('albums.index', $child))
        ->assertForbidden();
});

it('shows create album form', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->get(route('albums.create', $child))
        ->assertOk()
        ->assertSee('Tambah Album');
});

it('stores a new album', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->post(route('albums.store', $child), [
            'name' => 'Album Ulang Tahun',
            'description' => 'Momen ulang tahun pertama',
            'is_private' => '1',
            'sort_order' => 0,
        ])
        ->assertRedirect(route('albums.index', $child));

    assertDatabaseHas('albums', [
        'child_id' => $child->id,
        'name' => 'Album Ulang Tahun',
        'is_private' => true,
    ]);
});

it('validates required fields when storing album', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->post(route('albums.store', $child), [
            'name' => '',
        ])
        ->assertSessionHasErrors('name');
});

it('shows album detail page', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    $album = Album::factory()->create([
        'child_id' => $child->id,
        'name' => 'Album Detail',
    ]);

    actingAs($user)
        ->get(route('albums.show', [$child, $album]))
        ->assertOk()
        ->assertSee('Album Detail');
});

it('prevents other users from viewing album detail', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    $album = Album::factory()->create(['child_id' => $child->id]);

    actingAs($otherUser)
        ->get(route('albums.show', [$child, $album]))
        ->assertForbidden();
});

it('shows edit album form', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    $album = Album::factory()->create(['child_id' => $child->id, 'name' => 'Edit Me']);

    actingAs($user)
        ->get(route('albums.edit', [$child, $album]))
        ->assertOk()
        ->assertSee('Edit Album')
        ->assertSee('Edit Me');
});

it('updates an album', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    $album = Album::factory()->create(['child_id' => $child->id, 'name' => 'Old Name']);

    actingAs($user)
        ->put(route('albums.update', [$child, $album]), [
            'name' => 'New Name',
            'description' => 'Updated description',
            'is_private' => '0',
            'sort_order' => 5,
        ])
        ->assertRedirect(route('albums.show', [$child, $album]));

    assertDatabaseHas('albums', [
        'id' => $album->id,
        'name' => 'New Name',
        'is_private' => false,
        'sort_order' => 5,
    ]);
});

it('validates required fields when updating album', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    $album = Album::factory()->create(['child_id' => $child->id]);

    actingAs($user)
        ->put(route('albums.update', [$child, $album]), [
            'name' => '',
        ])
        ->assertSessionHasErrors('name');
});

it('deletes an album', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    $album = Album::factory()->create(['child_id' => $child->id, 'name' => 'To Delete']);

    actingAs($user)
        ->delete(route('albums.destroy', [$child, $album]))
        ->assertRedirect(route('albums.index', $child));

    assertDatabaseMissing('albums', ['id' => $album->id]);
});
