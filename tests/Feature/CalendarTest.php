<?php

use App\Models\Child;
use App\Models\Event;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

it('requires authentication to access calendar index', function () {
    $child = Child::factory()->create();

    $this->get(route('calendar.index', $child))
        ->assertRedirect(route('login'));
});

it('shows empty state when no events exist', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->get(route('calendar.index', $child))
        ->assertOk()
        ->assertSee('Belum Ada Acara');
});

it('lists events for a child', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    Event::factory()->create([
        'child_id' => $child->id,
        'user_id' => $user->id,
        'title' => 'Imunisasi PCV 2',
        'event_date' => now()->toDateString(),
    ]);

    actingAs($user)
        ->get(route('calendar.index', $child))
        ->assertOk()
        ->assertSee('Imunisasi PCV 2');
});

it('prevents other users from viewing events', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    actingAs($otherUser)
        ->get(route('calendar.index', $child))
        ->assertForbidden();
});

it('shows create event form', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->get(route('calendar.create', $child))
        ->assertOk()
        ->assertSee('Tambah Acara');
});

it('stores a new event', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->post(route('calendar.store', $child), [
            'title' => 'Imunisasi PCV 2',
            'event_date' => '2026-09-15',
            'event_time' => '09:00',
            'event_type' => 'immunization',
            'description' => 'Imunisasi kedua PCV',
            'is_recurring' => '0',
        ])
        ->assertRedirect(route('calendar.index', $child));

    assertDatabaseHas('events', [
        'child_id' => $child->id,
        'title' => 'Imunisasi PCV 2',
        'event_type' => 'immunization',
    ]);
});

it('validates required fields when storing event', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->post(route('calendar.store', $child), [
            'title' => '',
            'event_date' => '',
            'event_type' => '',
        ])
        ->assertSessionHasErrors(['title', 'event_date']);
});

it('validates event type enum when storing', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->post(route('calendar.store', $child), [
            'title' => 'Test Event',
            'event_date' => '2026-09-15',
            'event_type' => 'invalid_type',
        ])
        ->assertSessionHasErrors('event_type');
});

it('shows event detail page', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    $event = Event::factory()->create([
        'child_id' => $child->id,
        'user_id' => $user->id,
        'title' => 'Detail Event',
    ]);

    actingAs($user)
        ->get(route('calendar.show', [$child, $event]))
        ->assertOk()
        ->assertSee('Detail Event');
});

it('prevents other users from viewing event detail', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    $event = Event::factory()->create(['child_id' => $child->id, 'user_id' => $user->id]);

    actingAs($otherUser)
        ->get(route('calendar.show', [$child, $event]))
        ->assertForbidden();
});

it('shows edit event form', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    $event = Event::factory()->create([
        'child_id' => $child->id,
        'user_id' => $user->id,
        'title' => 'Edit Me',
    ]);

    actingAs($user)
        ->get(route('calendar.edit', [$child, $event]))
        ->assertOk()
        ->assertSee('Edit Acara')
        ->assertSee('Edit Me');
});

it('updates an event', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    $event = Event::factory()->create([
        'child_id' => $child->id,
        'user_id' => $user->id,
        'title' => 'Old Title',
    ]);

    actingAs($user)
        ->put(route('calendar.update', [$child, $event]), [
            'title' => 'New Title',
            'event_date' => '2026-10-01',
            'event_type' => 'birthday',
        ])
        ->assertRedirect(route('calendar.show', [$child, $event]));

    assertDatabaseHas('events', [
        'id' => $event->id,
        'title' => 'New Title',
        'event_type' => 'birthday',
    ]);
});

it('deletes an event', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    $event = Event::factory()->create([
        'child_id' => $child->id,
        'user_id' => $user->id,
        'title' => 'To Delete',
    ]);

    actingAs($user)
        ->delete(route('calendar.destroy', [$child, $event]))
        ->assertRedirect(route('calendar.index', $child));

    assertDatabaseMissing('events', ['id' => $event->id]);
});
