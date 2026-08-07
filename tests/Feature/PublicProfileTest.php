<?php

use App\Models\Child;
use App\Models\Timeline;
use App\Models\User;

it('shows public profile for a child with public enabled', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create([
        'user_id' => $user->id,
        'is_public' => true,
        'bio' => 'Aku suka bermain dan belajar.',
    ]);

    $this->get('/'.$child->slug)
        ->assertOk()
        ->assertSee($child->name)
        ->assertSee('Aku suka bermain dan belajar.');
});

it('returns 404 for non-public child', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create([
        'user_id' => $user->id,
        'is_public' => false,
    ]);

    $this->get('/'.$child->slug)
        ->assertNotFound();
});

it('returns 404 for non-existent slug', function () {
    $this->get('/non-existent-slug')
        ->assertNotFound();
});

it('does not require authentication', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create([
        'user_id' => $user->id,
        'is_public' => true,
    ]);

    $this->get('/'.$child->slug)
        ->assertOk();
});

it('displays child name and nickname', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create([
        'user_id' => $user->id,
        'is_public' => true,
        'nickname' => 'Mysha',
    ]);

    $this->get('/'.$child->slug)
        ->assertOk()
        ->assertSee($child->name)
        ->assertSee('Mysha');
});

it('displays gender badge', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create([
        'user_id' => $user->id,
        'is_public' => true,
        'gender' => 'female',
    ]);

    $this->get('/'.$child->slug)
        ->assertOk()
        ->assertSee('Perempuan');
});

it('displays age when available', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create([
        'user_id' => $user->id,
        'is_public' => true,
        'date_of_birth' => now()->subYears(3)->subMonths(6),
    ]);

    $this->get('/'.$child->slug)
        ->assertOk()
        ->assertSee('tahun');
});

it('displays place of birth when available', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create([
        'user_id' => $user->id,
        'is_public' => true,
        'place_of_birth' => 'Jakarta',
    ]);

    $this->get('/'.$child->slug)
        ->assertOk()
        ->assertSee('Jakarta');
});

it('displays blood type when available', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create([
        'user_id' => $user->id,
        'is_public' => true,
        'blood_type' => 'O+',
    ]);

    $this->get('/'.$child->slug)
        ->assertOk()
        ->assertSee('O+');
});

it('shows recent timelines when timeline is in public_profile_data', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create([
        'user_id' => $user->id,
        'is_public' => true,
        'public_profile_data' => ['timeline'],
    ]);

    Timeline::factory()->create([
        'child_id' => $child->id,
        'user_id' => $user->id,
        'title' => 'Pertama kali berjalan',
    ]);

    $this->get('/'.$child->slug)
        ->assertOk()
        ->assertSee('Pertama kali berjalan')
        ->assertSee('Timeline');
});

it('does not show timeline section when not in public_profile_data', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create([
        'user_id' => $user->id,
        'is_public' => true,
        'public_profile_data' => [],
    ]);

    Timeline::factory()->create([
        'child_id' => $child->id,
        'user_id' => $user->id,
        'title' => 'Pertama kali berjalan',
    ]);

    $this->get('/'.$child->slug)
        ->assertOk()
        ->assertDontSee('Timeline');
});

it('shows gallery section when gallery is in public_profile_data', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create([
        'user_id' => $user->id,
        'is_public' => true,
        'public_profile_data' => ['gallery'],
    ]);

    $this->get('/'.$child->slug)
        ->assertOk()
        ->assertSee('Galeri');
});

it('shows awards section when awards is in public_profile_data', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create([
        'user_id' => $user->id,
        'is_public' => true,
        'public_profile_data' => ['awards'],
    ]);

    $this->get('/'.$child->slug)
        ->assertOk()
        ->assertSee('Penghargaan');
});

it('does not show gallery or awards when not in public_profile_data', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create([
        'user_id' => $user->id,
        'is_public' => true,
        'public_profile_data' => [],
    ]);

    $this->get('/'.$child->slug)
        ->assertOk()
        ->assertDontSee('Galeri')
        ->assertDontSee('Penghargaan');
});

it('shows ForMysha branding in header and footer', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create([
        'user_id' => $user->id,
        'is_public' => true,
    ]);

    $this->get('/'.$child->slug)
        ->assertOk()
        ->assertSee('ForMysha')
        ->assertSee('Every Moment, Every Memory, One Lifetime.');
});

it('uses guest layout not app layout', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create([
        'user_id' => $user->id,
        'is_public' => true,
    ]);

    $this->get('/'.$child->slug)
        ->assertOk()
        ->assertDontSee('x-app-layout');
});
