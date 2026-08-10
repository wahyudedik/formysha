<?php

use App\Models\Achievement;
use App\Models\Child;
use App\Models\Timeline;
use App\Models\User;
use App\Services\AchievementService;

it('awards first_timeline achievement when timeline exists', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    Timeline::factory()->create([
        'user_id' => $user->id,
        'child_id' => $child->id,
    ]);

    $service = app(AchievementService::class);
    $newlyEarned = $service->checkAchievements($user, $child);

    expect($newlyEarned)->not->toBeEmpty();
    expect($newlyEarned[0]->type)->toBe('first_timeline');
    expect($newlyEarned[0]->isEarned())->toBeTrue();
});

it('does not award same achievement twice', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    Timeline::factory()->create([
        'user_id' => $user->id,
        'child_id' => $child->id,
    ]);

    $service = app(AchievementService::class);

    // First check — should award
    $first = $service->checkAchievements($user, $child);
    expect($first)->not->toBeEmpty();

    // Second check — should not award again
    $second = $service->checkAchievements($user, $child);
    expect($second)->toBeEmpty();
});

it('returns all achievements with earned status', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    Achievement::factory()->earned()->ofType('first_upload')->create([
        'user_id' => $user->id,
        'child_id' => $child->id,
    ]);

    $service = app(AchievementService::class);
    $achievements = $service->getAchievements($child);

    expect($achievements)->toHaveCount(count(Achievement::TYPES));

    $firstUpload = collect($achievements)->firstWhere('type', 'first_upload');
    expect($firstUpload['earned'])->toBeTrue();

    $tenPhotos = collect($achievements)->firstWhere('type', 'ten_photos');
    expect($tenPhotos['earned'])->toBeFalse();
});

it('counts earned achievements correctly', function () {
    $user = User::factory()->create();
    $child = Child::factory()->create(['user_id' => $user->id]);

    Achievement::factory()->earned()->ofType('first_upload')->create([
        'user_id' => $user->id,
        'child_id' => $child->id,
    ]);

    Achievement::factory()->earned()->ofType('first_timeline')->create([
        'user_id' => $user->id,
        'child_id' => $child->id,
    ]);

    $service = app(AchievementService::class);

    expect($service->getEarnedCount($child))->toBe(2);
});
