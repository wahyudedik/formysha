<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\Child;
use App\Models\User;
use Carbon\Carbon;

class AchievementService
{
    /**
     * Check and award all applicable achievements for a child.
     *
     * @return Achievement[] Newly earned achievements
     */
    public function checkAchievements(User $user, Child $child): array
    {
        $newlyEarned = [];

        $checks = [
            'first_upload' => fn () => $child->media()->count() >= 1,
            'first_timeline' => fn () => $child->timelines()->count() >= 1,
            'first_diary' => fn () => $child->diaries()->count() >= 1,
            'ten_photos' => fn () => $child->media()->where('file_type', 'photo')->count() >= 10,
            'fifty_photos' => fn () => $child->media()->where('file_type', 'photo')->count() >= 50,
            'hundred_photos' => fn () => $child->media()->where('file_type', 'photo')->count() >= 100,
            'health_tracker' => fn () => $child->healthRecords()->count() >= 5,
            'growth_tracker' => fn () => $child->growths()->count() >= 10,
            'family_builder' => fn () => $child->familyMembers()->count() >= 3,
            'document_keeper' => fn () => $child->documents()->count() >= 5,
            'one_year_streak' => fn () => $this->hasOneYearStreak($user, $child),
        ];

        foreach ($checks as $type => $check) {
            if ($check()) {
                $achievement = $this->awardIfNotEarned($user, $child, $type);
                if ($achievement) {
                    $newlyEarned[] = $achievement;
                }
            }
        }

        return $newlyEarned;
    }

    /**
     * Award an achievement if not already earned.
     */
    public function awardIfNotEarned(User $user, Child $child, string $type): ?Achievement
    {
        $meta = Achievement::TYPES[$type] ?? null;
        if (! $meta) {
            return null;
        }

        $existing = Achievement::where('user_id', $user->id)
            ->where('child_id', $child->id)
            ->where('type', $type)
            ->first();

        if ($existing && $existing->isEarned()) {
            return null;
        }

        if ($existing) {
            $existing->update(['earned_at' => now()]);

            return $existing->fresh();
        }

        return Achievement::create([
            'user_id' => $user->id,
            'child_id' => $child->id,
            'type' => $type,
            'name' => $meta['name'],
            'description' => $meta['description'],
            'icon' => $meta['icon'],
            'earned_at' => now(),
        ]);
    }

    /**
     * Get all achievements for a child (earned + pending).
     */
    public function getAchievements(Child $child): array
    {
        $earned = Achievement::where('child_id', $child->id)
            ->earned()
            ->get()
            ->pluck('type')
            ->toArray();

        $all = [];
        foreach (Achievement::TYPES as $type => $meta) {
            $all[] = [
                'type' => $type,
                'name' => $meta['name'],
                'description' => $meta['description'],
                'icon' => $meta['icon'],
                'earned' => in_array($type, $earned),
            ];
        }

        return $all;
    }

    /**
     * Get earned achievement count for a child.
     */
    public function getEarnedCount(Child $child): int
    {
        return Achievement::where('child_id', $child->id)->earned()->count();
    }

    /**
     * Check if user has been active for at least one year with this child.
     */
    private function hasOneYearStreak(User $user, Child $child): bool
    {
        $firstRecord = $child->timelines()->oldest('created_at')->first()
            ?? $child->media()->oldest('created_at')->first()
            ?? $child->diaries()->oldest('created_at')->first();

        if (! $firstRecord) {
            return false;
        }

        return Carbon::parse($firstRecord->created_at)->diffInYears(now()) >= 1;
    }
}
