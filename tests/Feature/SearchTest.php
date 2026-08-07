<?php

use App\Models\Child;
use App\Models\Diary;
use App\Models\Document;
use App\Models\HealthRecord;
use App\Models\Timeline;
use App\Models\User;

use function Pest\Laravel\actingAs;

it('requires authentication to access search', function () {
    $this->get(route('search.index', ['q' => 'test']))
        ->assertRedirect(route('login'));
});

it('shows search form with empty state', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(route('search.index'))
        ->assertOk()
        ->assertSee('Mulai Mencari');
});

it('shows empty state for short query', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(route('search.index', ['q' => 'a']))
        ->assertOk()
        ->assertSee('Mulai Mencari');
});

it('searches timelines by title', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    Timeline::factory()->create([
        'child_id' => $child->id,
        'user_id' => $user->id,
        'title' => 'Pertama kali jalan',
    ]);

    actingAs($user)
        ->get(route('search.index', ['q' => 'Pertama']))
        ->assertOk()
        ->assertSee('Pertama kali jalan')
        ->assertSee('Ditemukan');
});

it('searches diaries by title', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    Diary::factory()->create([
        'child_id' => $child->id,
        'user_id' => $user->id,
        'title' => 'Hari yang menyenangkan',
    ]);

    actingAs($user)
        ->get(route('search.index', ['q' => 'menyenangkan']))
        ->assertOk()
        ->assertSee('Hari yang menyenangkan');
});

it('searches documents by name', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    Document::factory()->create([
        'child_id' => $child->id,
        'user_id' => $user->id,
        'name' => 'Akta Kelahiran',
    ]);

    actingAs($user)
        ->get(route('search.index', ['q' => 'Akta']))
        ->assertOk()
        ->assertSee('Akta Kelahiran');
});

it('searches health records by name', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    HealthRecord::factory()->immunization()->create([
        'child_id' => $child->id,
        'user_id' => $user->id,
        'name' => 'BCG',
    ]);

    actingAs($user)
        ->get(route('search.index', ['q' => 'BCG']))
        ->assertOk()
        ->assertSee('BCG');
});

it('filters search by module', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    Timeline::factory()->create([
        'child_id' => $child->id,
        'user_id' => $user->id,
        'title' => 'Main di taman',
    ]);
    Diary::factory()->create([
        'child_id' => $child->id,
        'user_id' => $user->id,
        'title' => 'Main di taman juga',
    ]);

    actingAs($user)
        ->get(route('search.index', ['q' => 'Main', 'module' => 'timeline']))
        ->assertOk()
        ->assertSee('Main di taman')
        ->assertDontSee('Main di taman juga');
});

it('does not show results from other users', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $otherUser->id]);
    Timeline::factory()->create([
        'child_id' => $child->id,
        'user_id' => $otherUser->id,
        'title' => 'Rahasia orang lain',
    ]);

    actingAs($user)
        ->get(route('search.index', ['q' => 'Rahasia']))
        ->assertOk()
        ->assertDontSee('Rahasia orang lain')
        ->assertSee('Tidak Ada Hasil');
});

it('shows result counts per module', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    Timeline::factory()->count(2)->create([
        'child_id' => $child->id,
        'user_id' => $user->id,
        'title' => 'Test item',
    ]);

    actingAs($user)
        ->get(route('search.index', ['q' => 'Test']))
        ->assertOk()
        ->assertSee('(2)');
});

it('is case insensitive', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);
    Timeline::factory()->create([
        'child_id' => $child->id,
        'user_id' => $user->id,
        'title' => 'Pertumbuhan Sehat',
    ]);

    actingAs($user)
        ->get(route('search.index', ['q' => 'pertumbuhan']))
        ->assertOk()
        ->assertSee('Pertumbuhan Sehat');
});
