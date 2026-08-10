<?php

use App\Models\Achievement;
use App\Models\Child;
use App\Models\Timeline;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

it('requires authentication to access achievements index', function () {
    $child = Child::factory()->create();

    $this->get(route('achievements.index', $child))
        ->assertRedirect(route('login'));
});

it('shows achievements page for a child', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->get(route('achievements.index', $child))
        ->assertOk()
        ->assertSee('Pencapaian')
        ->assertSee($child->name);
});

it('prevents other users from viewing achievements', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    actingAs($otherUser)
        ->get(route('achievements.index', $child))
        ->assertForbidden();
});

it('displays earned achievements', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    Achievement::factory()->earned()->ofType('first_upload')->create([
        'user_id' => $user->id,
        'child_id' => $child->id,
    ]);

    actingAs($user)
        ->get(route('achievements.index', $child))
        ->assertOk()
        ->assertSee('Foto Pertama')
        ->assertSee('Terbuka');
});

it('displays locked achievements', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->get(route('achievements.index', $child))
        ->assertOk()
        ->assertSee('Terkunci');
});

it('can trigger achievement check', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->post(route('achievements.check', $child))
        ->assertRedirect(route('achievements.index', $child));
});

it('awards first_timeline achievement when timeline exists', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    // Create a timeline for the child
    Timeline::factory()->create([
        'user_id' => $user->id,
        'child_id' => $child->id,
    ]);

    actingAs($user)
        ->post(route('achievements.check', $child))
        ->assertRedirect();

    assertDatabaseHas('achievements', [
        'user_id' => $user->id,
        'child_id' => $child->id,
        'type' => 'first_timeline',
    ]);
});
