<?php

use App\Models\Child;
use App\Models\Event;
use App\Models\User;

it('can create an event', function () {
    $event = Event::factory()->create();

    expect($event)->toBeInstanceOf(Event::class);
    expect($event->title)->toBeString();
    expect($event->event_date)->toBeString();
    expect($event->child_id)->toBeInt();
    expect($event->user_id)->toBeInt();
});

it('belongs to a child', function () {
    $event = Event::factory()->create();

    expect($event->child)->not->toBeNull();
    expect($event->child)->toBeInstanceOf(Child::class);
});

it('belongs to a user', function () {
    $event = Event::factory()->create();

    expect($event->user)->not->toBeNull();
    expect($event->user)->toBeInstanceOf(User::class);
});

it('has correct fillable attributes', function () {
    $event = new Event;

    expect($event->getFillable())->toBe([
        'child_id',
        'user_id',
        'title',
        'description',
        'event_date',
        'event_time',
        'event_type',
        'is_recurring',
        'recurrence_pattern',
        'reminder_at',
    ]);
});

it('casts is_recurring as boolean', function () {
    $event = Event::factory()->create(['is_recurring' => true]);

    expect($event->is_recurring)->toBeTrue();
});

it('returns correct event type labels', function () {
    $types = [
        'birthday' => '🎂 Ulang Tahun',
        'immunization' => '💉 Imunisasi',
        'appointment' => '🩺 Janji Temu',
        'school' => '🏫 Sekolah',
        'other' => '📌 Lainnya',
    ];

    foreach ($types as $type => $label) {
        $event = Event::factory()->create(['event_type' => $type]);
        expect($event->event_type_label)->toBe($label);
    }
});

it('returns formatted date', function () {
    $event = Event::factory()->create(['event_date' => '2026-08-07']);

    expect($event->formatted_date)->toBeString();
    expect($event->formatted_date)->toContain('2026');
});

it('returns formatted time', function () {
    $event = Event::factory()->create(['event_time' => '14:30']);

    expect($event->formatted_time)->toBeString();
    expect($event->formatted_time)->toContain('WIB');
});

it('returns null formatted time when not set', function () {
    $event = Event::factory()->create(['event_time' => null]);

    expect($event->formatted_time)->toBeNull();
});

it('detects upcoming event', function () {
    $event = Event::factory()->create(['event_date' => '2099-12-31']);

    expect($event->is_upcoming)->toBeTrue();
});

it('detects past event', function () {
    $event = Event::factory()->create(['event_date' => '2020-01-01']);

    expect($event->is_upcoming)->toBeFalse();
});

it('can be created with birthday state', function () {
    $event = Event::factory()->birthday()->create();

    expect($event->event_type)->toBe('birthday');
});

it('can be created with immunization state', function () {
    $event = Event::factory()->immunization()->create();

    expect($event->event_type)->toBe('immunization');
});

it('can be created with specific date state', function () {
    $event = Event::factory()->forDate('2026-12-25')->create();

    expect($event->event_date)->toBe('2026-12-25');
});

it('child has many events', function () {
    $child = Child::factory()->create();
    $user = User::factory()->create();
    Event::factory()->create(['child_id' => $child->id, 'user_id' => $user->id]);
    Event::factory()->create(['child_id' => $child->id, 'user_id' => $user->id]);

    expect($child->events)->toHaveCount(2);
});
