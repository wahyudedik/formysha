<?php

use App\Models\Child;
use App\Models\Diary;
use App\Models\User;
use Carbon\Carbon;

it('can create a diary entry', function () {
    $diary = Diary::factory()->create();

    expect($diary)->toBeInstanceOf(Diary::class);
    expect($diary->title)->toBeString();
    expect($diary->content)->toBeString();
    expect($diary->child_id)->toBeInt();
    expect($diary->user_id)->toBeInt();
});

it('belongs to a child', function () {
    $diary = Diary::factory()->create();

    expect($diary->child)->not->toBeNull();
    expect($diary->child)->toBeInstanceOf(Child::class);
});

it('belongs to a user', function () {
    $diary = Diary::factory()->create();

    expect($diary->user)->not->toBeNull();
    expect($diary->user)->toBeInstanceOf(User::class);
});

it('has correct fillable attributes', function () {
    $diary = new Diary;

    expect($diary->getFillable())->toBe([
        'child_id',
        'user_id',
        'title',
        'content',
        'mood',
        'diary_date',
        'weather',
        'is_private',
    ]);
});

it('casts is_private as boolean', function () {
    $diary = Diary::factory()->create(['is_private' => true]);

    expect($diary->is_private)->toBeTrue();
});

it('casts diary_date as date', function () {
    $diary = Diary::factory()->create(['diary_date' => '2026-08-07']);

    expect($diary->diary_date)->toBeInstanceOf(Carbon::class);
});

it('returns correct mood labels', function () {
    $diary = Diary::factory()->create(['mood' => 'happy']);
    expect($diary->mood_label)->toBe('😊 Bahagia');

    $diary = Diary::factory()->create(['mood' => 'excited']);
    expect($diary->mood_label)->toBe('🤩 Antusias');

    $diary = Diary::factory()->create(['mood' => 'calm']);
    expect($diary->mood_label)->toBe('😌 Tenang');

    $diary = Diary::factory()->create(['mood' => 'sad']);
    expect($diary->mood_label)->toBe('😢 Sedih');

    $diary = Diary::factory()->create(['mood' => 'surprised']);
    expect($diary->mood_label)->toBe('😲 Terkejut');

    $diary = Diary::factory()->create(['mood' => 'loved']);
    expect($diary->mood_label)->toBe('🥰 Disayang');
});

it('returns correct weather labels', function () {
    $diary = Diary::factory()->create(['weather' => 'sunny']);
    expect($diary->weather_label)->toBe('☀️ Cerah');

    $diary = Diary::factory()->create(['weather' => 'cloudy']);
    expect($diary->weather_label)->toBe('☁️ Berawan');

    $diary = Diary::factory()->create(['weather' => 'rainy']);
    expect($diary->weather_label)->toBe('🌧️ Hujan');

    $diary = Diary::factory()->create(['weather' => 'windy']);
    expect($diary->weather_label)->toBe('💨 Berangin');

    $diary = Diary::factory()->create(['weather' => 'snowy']);
    expect($diary->weather_label)->toBe('❄️ Bersalju');
});

it('returns formatted date', function () {
    $diary = Diary::factory()->create(['diary_date' => '2026-08-07']);

    expect($diary->formatted_date)->toBeString();
    expect($diary->formatted_date)->toContain('2026');
});

it('can be created with public state', function () {
    $diary = Diary::factory()->public()->create();

    expect($diary->is_private)->toBeFalse();
});

it('can be created with private state', function () {
    $diary = Diary::factory()->private()->create();

    expect($diary->is_private)->toBeTrue();
});

it('child has many diaries', function () {
    $child = Child::factory()->create();
    $user = User::factory()->create();
    Diary::factory()->create(['child_id' => $child->id, 'user_id' => $user->id]);
    Diary::factory()->create(['child_id' => $child->id, 'user_id' => $user->id]);

    expect($child->diaries)->toHaveCount(2);
});
