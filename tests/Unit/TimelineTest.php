<?php

use App\Models\Child;
use App\Models\Media;
use App\Models\Timeline;
use App\Models\User;
use Carbon\Carbon;

it('can create a timeline', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    $timeline = Timeline::factory()->create([
        'child_id' => $child->id,
        'user_id' => $user->id,
    ]);

    $this->assertNotNull($timeline);
    $this->assertDatabaseHas('timelines', ['id' => $timeline->id]);
});

it('belongs to a child', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    $timeline = Timeline::factory()->create([
        'child_id' => $child->id,
        'user_id' => $user->id,
    ]);

    $this->assertInstanceOf(Child::class, $timeline->child);
    $this->assertEquals($child->id, $timeline->child_id);
});

it('belongs to a user', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    $timeline = Timeline::factory()->create([
        'child_id' => $child->id,
        'user_id' => $user->id,
    ]);

    $this->assertInstanceOf(User::class, $timeline->user);
    $this->assertEquals($user->id, $timeline->user_id);
});

it('has many media', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    $timeline = Timeline::factory()->create([
        'child_id' => $child->id,
        'user_id' => $user->id,
    ]);
    Media::factory()->count(3)->create([
        'mediable_type' => Timeline::class,
        'mediable_id' => $timeline->id,
    ]);

    $this->assertCount(3, $timeline->media);
});

it('returns correct mood labels', function () {
    $timeline = Timeline::factory()->create(['mood' => 'happy']);
    $this->assertEquals('😊 Bahagia', $timeline->mood_label);

    $timeline = Timeline::factory()->create(['mood' => 'excited']);
    $this->assertEquals('🤩 Antusias', $timeline->mood_label);

    $timeline = Timeline::factory()->create(['mood' => 'loved']);
    $this->assertEquals('🥰 Disayang', $timeline->mood_label);

    $timeline = Timeline::factory()->create(['mood' => null]);
    $this->assertEquals('-', $timeline->mood_label);
});

it('returns correct formatted date', function () {
    $timeline = Timeline::factory()->create(['event_date' => '2026-08-15']);
    $formatted = $timeline->formatted_date;

    $this->assertIsString($formatted);
    $this->assertNotEmpty($formatted);
});

it('casts is_featured as boolean', function () {
    $timeline = Timeline::factory()->create(['is_featured' => true]);
    $this->assertIsBool($timeline->is_featured);
    $this->assertTrue($timeline->is_featured);

    $timeline = Timeline::factory()->create(['is_featured' => false]);
    $this->assertFalse($timeline->is_featured);
});

it('casts tags as array', function () {
    $timeline = Timeline::factory()->create(['tags' => ['milestone', 'family']]);
    $this->assertIsArray($timeline->tags);
    $this->assertCount(2, $timeline->tags);
});

it('casts event_date as date', function () {
    $timeline = Timeline::factory()->create(['event_date' => '2026-01-15']);
    $this->assertInstanceOf(Carbon::class, $timeline->event_date);
});

it('can create with featured state', function () {
    $timeline = Timeline::factory()->featured()->create();
    $this->assertTrue($timeline->is_featured);
});

it('can create with mood state', function () {
    $timeline = Timeline::factory()->mood('happy')->create();
    $this->assertEquals('happy', $timeline->mood);
});

it('can create with tags state', function () {
    $timeline = Timeline::factory()->withTags(['test', 'demo'])->create();
    $this->assertEquals(['test', 'demo'], $timeline->tags);
});

it('child has many timelines', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    Timeline::factory()->count(3)->create([
        'child_id' => $child->id,
        'user_id' => $user->id,
    ]);

    $this->assertCount(3, $child->timelines);
});
