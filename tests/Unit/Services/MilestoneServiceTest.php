<?php

use App\Models\Child;
use App\Models\MilestoneAlert;
use App\Models\User;
use App\Services\MilestoneService;

it('creates birthday milestone alert for upcoming birthday', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create([
        'user_id' => $user->id,
        'date_of_birth' => now()->subYears(2)->addDays(3)->toDateString(),
    ]);

    $service = app(MilestoneService::class);
    $alerts = $service->checkMilestones($user, $child);

    expect($alerts)->not->toBeEmpty();

    $birthdayAlert = collect($alerts)->firstWhere('type', MilestoneAlert::TYPE_BIRTHDAY);
    expect($birthdayAlert)->not->toBeNull();
    expect($birthdayAlert->title)->toContain($child->name);
});

it('does not create duplicate milestone alerts for same type', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create([
        'user_id' => $user->id,
        'date_of_birth' => now()->subYears(2)->addDays(3)->toDateString(),
    ]);

    $service = app(MilestoneService::class);

    // First check — should create birthday alert
    $first = $service->checkMilestones($user, $child);
    expect($first)->not->toBeEmpty();

    // Count birthday alerts after first check
    $birthdayCount = MilestoneAlert::where('child_id', $child->id)
        ->where('type', MilestoneAlert::TYPE_BIRTHDAY)
        ->count();

    // Second check — should not create another birthday alert
    $service->checkMilestones($user, $child);

    $birthdayCountAfter = MilestoneAlert::where('child_id', $child->id)
        ->where('type', MilestoneAlert::TYPE_BIRTHDAY)
        ->count();

    expect($birthdayCountAfter)->toBe($birthdayCount);
});

it('returns active milestones for a child', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    // Directly create a milestone alert
    $alert = MilestoneAlert::create([
        'user_id' => $user->id,
        'child_id' => $child->id,
        'type' => MilestoneAlert::TYPE_BIRTHDAY,
        'title' => 'Ulang Tahun Test',
        'description' => 'Test milestone',
        'icon' => '🎂',
        'alert_date' => now()->subDay()->toDateString(),
        'milestone_date' => now()->addDays(5)->toDateString(),
        'is_dismissed' => false,
    ]);

    $milestones = MilestoneAlert::where('child_id', $child->id)
        ->active()
        ->upcoming()
        ->get();

    expect($milestones->count())->toBeGreaterThanOrEqual(1);
    expect($milestones->first()->id)->toBe($alert->id);
});

it('does not return dismissed milestones in upcoming', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    MilestoneAlert::factory()->dismissed()->ofType('birthday')->create([
        'user_id' => $user->id,
        'child_id' => $child->id,
    ]);

    $milestones = MilestoneAlert::where('child_id', $child->id)
        ->active()
        ->upcoming()
        ->get();

    expect($milestones)->toHaveCount(0);
});

it('creates growth record reminder when no recent records', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    $service = app(MilestoneService::class);
    $alerts = $service->checkMilestones($user, $child);

    $growthAlert = collect($alerts)->firstWhere('type', MilestoneAlert::TYPE_GROWTH_RECORD);
    expect($growthAlert)->not->toBeNull();
    expect($growthAlert->type)->toBe(MilestoneAlert::TYPE_GROWTH_RECORD);
});

it('dismisses a milestone alert correctly', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    $alert = MilestoneAlert::factory()->ofType('birthday')->create([
        'user_id' => $user->id,
        'child_id' => $child->id,
    ]);

    $service = app(MilestoneService::class);
    $result = $service->dismiss($alert);

    expect($result)->toBeTrue();
    expect($alert->fresh()->is_dismissed)->toBeTrue();
    expect($alert->fresh()->dismissed_at)->not->toBeNull();
});
