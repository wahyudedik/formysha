<?php

use App\Models\Child;
use App\Models\Timeline;
use App\Models\User;

it('requires authentication to access timeline', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    $this->get(route('timeline.index', $child))
        ->assertRedirect('/login');
});

it('shows timeline index for authenticated user', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->get(route('timeline.index', $child))
        ->assertOk()
        ->assertSee('Timeline');
});

it('shows empty state when no timelines exist', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->get(route('timeline.index', $child))
        ->assertOk()
        ->assertSee('Belum Ada Kenangan');
});

it('lists timelines for a child', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    $timeline = Timeline::factory()->create([
        'child_id' => $child->id,
        'user_id' => $user->id,
        'title' => 'Hari Pertama Berenang',
    ]);

    $this->actingAs($user)
        ->get(route('timeline.index', $child))
        ->assertOk()
        ->assertSee('Hari Pertama Berenang');
});

it('prevents other users from viewing timelines', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    $this->actingAs($otherUser)
        ->get(route('timeline.index', $child))
        ->assertForbidden();
});

it('shows create form', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->get(route('timeline.create', $child))
        ->assertOk()
        ->assertSee('Tambah Kenangan');
});

it('stores a new timeline entry', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->post(route('timeline.store', $child), [
            'title' => 'Hari Pertama Sekolah',
            'description' => 'Hari yang sangat berkesan',
            'event_date' => '2026-08-07',
            'event_time' => '08:00',
            'location' => 'SDN 01 Jakarta',
            'mood' => 'excited',
            'tags' => ['milestone', 'sekolah'],
            'is_featured' => true,
        ])
        ->assertRedirect(route('timeline.index', $child));

    $this->assertDatabaseHas('timelines', [
        'child_id' => $child->id,
        'user_id' => $user->id,
        'title' => 'Hari Pertama Sekolah',
    ]);
});

it('validates required fields when storing', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->post(route('timeline.store', $child), [])
        ->assertSessionHasErrors(['title', 'event_date']);
});

it('validates mood enum when storing', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->post(route('timeline.store', $child), [
            'title' => 'Test',
            'event_date' => '2026-08-07',
            'mood' => 'invalid_mood',
        ])
        ->assertSessionHasErrors(['mood']);
});

it('shows timeline detail', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    $timeline = Timeline::factory()->create([
        'child_id' => $child->id,
        'user_id' => $user->id,
        'title' => 'Ulang Tahun Pertama',
    ]);

    $this->actingAs($user)
        ->get(route('timeline.show', [$child, $timeline]))
        ->assertOk()
        ->assertSee('Ulang Tahun Pertama');
});

it('prevents other users from viewing timeline detail', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    $timeline = Timeline::factory()->create([
        'child_id' => $child->id,
        'user_id' => $user->id,
    ]);

    $this->actingAs($otherUser)
        ->get(route('timeline.show', [$child, $timeline]))
        ->assertForbidden();
});

it('shows edit form', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    $timeline = Timeline::factory()->create([
        'child_id' => $child->id,
        'user_id' => $user->id,
    ]);

    $this->actingAs($user)
        ->get(route('timeline.edit', [$child, $timeline]))
        ->assertOk()
        ->assertSee('Edit Kenangan');
});

it('updates a timeline entry', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    $timeline = Timeline::factory()->create([
        'child_id' => $child->id,
        'user_id' => $user->id,
        'title' => 'Old Title',
    ]);

    $this->actingAs($user)
        ->put(route('timeline.update', [$child, $timeline]), [
            'title' => 'New Title',
            'event_date' => $timeline->event_date->format('Y-m-d'),
        ])
        ->assertRedirect(route('timeline.show', [$child, $timeline]));

    $this->assertDatabaseHas('timelines', [
        'id' => $timeline->id,
        'title' => 'New Title',
    ]);
});

it('deletes a timeline entry', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    $timeline = Timeline::factory()->create([
        'child_id' => $child->id,
        'user_id' => $user->id,
    ]);

    $this->actingAs($user)
        ->delete(route('timeline.destroy', [$child, $timeline]))
        ->assertRedirect(route('timeline.index', $child));

    $this->assertDatabaseMissing('timelines', ['id' => $timeline->id]);
});
