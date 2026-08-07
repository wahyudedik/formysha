<?php

use App\Models\Child;
use App\Models\Diary;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

it('requires authentication to access diary index', function () {
    $child = Child::factory()->create();

    $this->get(route('diaries.index', $child))
        ->assertRedirect(route('login'));
});

it('shows empty state when no diary entries exist', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->get(route('diaries.index', $child))
        ->assertOk()
        ->assertSee('Belum Ada Catatan');
});

it('lists diary entries for a child', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    Diary::factory()->create([
        'child_id' => $child->id,
        'user_id' => $user->id,
        'title' => 'Hari Pertama Sekolah',
    ]);

    actingAs($user)
        ->get(route('diaries.index', $child))
        ->assertOk()
        ->assertSee('Hari Pertama Sekolah');
});

it('prevents other users from viewing diary entries', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    actingAs($otherUser)
        ->get(route('diaries.index', $child))
        ->assertForbidden();
});

it('shows create diary form', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->get(route('diaries.create', $child))
        ->assertOk()
        ->assertSee('Tulis Diary');
});

it('stores a new diary entry', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->post(route('diaries.store', $child), [
            'title' => 'Cerita Hari Ini',
            'content' => 'Hari ini sangat menyenangkan.',
            'diary_date' => '2026-08-07',
            'mood' => 'happy',
            'weather' => 'sunny',
            'is_private' => '1',
        ])
        ->assertRedirect(route('diaries.index', $child));

    assertDatabaseHas('diaries', [
        'child_id' => $child->id,
        'title' => 'Cerita Hari Ini',
        'is_private' => true,
    ]);
});

it('validates required fields when storing diary', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->post(route('diaries.store', $child), [
            'title' => '',
            'content' => '',
        ])
        ->assertSessionHasErrors(['title', 'content']);
});

it('validates mood enum when storing diary', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->post(route('diaries.store', $child), [
            'title' => 'Test',
            'content' => 'Test content',
            'diary_date' => '2026-08-07',
            'mood' => 'invalid_mood',
        ])
        ->assertSessionHasErrors('mood');
});

it('shows diary detail page', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    $diary = Diary::factory()->create([
        'child_id' => $child->id,
        'user_id' => $user->id,
        'title' => 'Detail Diary',
    ]);

    actingAs($user)
        ->get(route('diaries.show', [$child, $diary]))
        ->assertOk()
        ->assertSee('Detail Diary');
});

it('prevents other users from viewing diary detail', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    $diary = Diary::factory()->create(['child_id' => $child->id, 'user_id' => $user->id]);

    actingAs($otherUser)
        ->get(route('diaries.show', [$child, $diary]))
        ->assertForbidden();
});

it('shows edit diary form', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    $diary = Diary::factory()->create([
        'child_id' => $child->id,
        'user_id' => $user->id,
        'title' => 'Edit Me',
    ]);

    actingAs($user)
        ->get(route('diaries.edit', [$child, $diary]))
        ->assertOk()
        ->assertSee('Edit Diary')
        ->assertSee('Edit Me');
});

it('updates a diary entry', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    $diary = Diary::factory()->create([
        'child_id' => $child->id,
        'user_id' => $user->id,
        'title' => 'Old Title',
    ]);

    actingAs($user)
        ->put(route('diaries.update', [$child, $diary]), [
            'title' => 'New Title',
            'content' => 'Updated content',
            'diary_date' => '2026-08-07',
            'mood' => 'excited',
            'weather' => 'cloudy',
            'is_private' => '0',
        ])
        ->assertRedirect(route('diaries.show', [$child, $diary]));

    assertDatabaseHas('diaries', [
        'id' => $diary->id,
        'title' => 'New Title',
        'mood' => 'excited',
        'is_private' => false,
    ]);
});

it('deletes a diary entry', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    $diary = Diary::factory()->create([
        'child_id' => $child->id,
        'user_id' => $user->id,
        'title' => 'To Delete',
    ]);

    actingAs($user)
        ->delete(route('diaries.destroy', [$child, $diary]))
        ->assertRedirect(route('diaries.index', $child));

    assertDatabaseMissing('diaries', ['id' => $diary->id]);
});
