<?php

use App\Models\Child;
use App\Models\MilestoneAlert;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

it('requires authentication to access milestones index', function () {
    $child = Child::factory()->create();

    $this->get(route('milestones.index', $child))
        ->assertRedirect(route('login'));
});

it('shows milestones page for a child', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->get(route('milestones.index', $child))
        ->assertOk()
        ->assertSee('Milestone')
        ->assertSee($child->name);
});

it('prevents other users from viewing milestones', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    actingAs($otherUser)
        ->get(route('milestones.index', $child))
        ->assertForbidden();
});

it('displays active milestones', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    MilestoneAlert::factory()->ofType('birthday')->create([
        'user_id' => $user->id,
        'child_id' => $child->id,
        'milestone_date' => now()->addDays(3)->toDateString(),
    ]);

    actingAs($user)
        ->get(route('milestones.index', $child))
        ->assertOk()
        ->assertSee('Milestone Aktif');
});

it('can trigger milestone check', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->post(route('milestones.check', $child))
        ->assertRedirect(route('milestones.index', $child));
});

it('can dismiss a milestone alert', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    $alert = MilestoneAlert::factory()->ofType('birthday')->create([
        'user_id' => $user->id,
        'child_id' => $child->id,
    ]);

    actingAs($user)
        ->post(route('milestones.dismiss', [$child, $alert]))
        ->assertRedirect(route('milestones.index', $child));

    assertDatabaseHas('milestone_alerts', [
        'id' => $alert->id,
        'is_dismissed' => true,
    ]);
});

it('prevents dismissing other users milestone alert', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    $alert = MilestoneAlert::factory()->ofType('birthday')->create([
        'user_id' => $user->id,
        'child_id' => $child->id,
    ]);

    actingAs($otherUser)
        ->post(route('milestones.dismiss', [$child, $alert]))
        ->assertForbidden();
});
