<?php

namespace App\Services;

use App\Models\Child;
use App\Models\MilestoneAlert;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class MilestoneService
{
    /**
     * Check and generate milestone alerts for a child.
     *
     * @return MilestoneAlert[] Newly created alerts
     */
    public function checkMilestones(User $user, Child $child): array
    {
        $newAlerts = [];

        $birthday = $this->checkBirthday($user, $child);
        if ($birthday) {
            $newAlerts[] = $birthday;
        }

        $monthlyAge = $this->checkMonthlyAge($user, $child);
        if ($monthlyAge) {
            $newAlerts[] = $monthlyAge;
        }

        $yearlyAge = $this->checkYearlyAge($user, $child);
        if ($yearlyAge) {
            $newAlerts[] = $yearlyAge;
        }

        $growth = $this->checkGrowthRecord($user, $child);
        if ($growth) {
            $newAlerts[] = $growth;
        }

        $immunization = $this->checkImmunization($user, $child);
        if ($immunization) {
            $newAlerts[] = $immunization;
        }

        return $newAlerts;
    }

    /**
     * Check all children for a user and generate milestone alerts.
     *
     * @return MilestoneAlert[] All newly created alerts
     */
    public function checkAllChildren(User $user): array
    {
        $allAlerts = [];

        $children = $user->children()->get();

        foreach ($children as $child) {
            $alerts = $this->checkMilestones($user, $child);
            $allAlerts = array_merge($allAlerts, $alerts);
        }

        return $allAlerts;
    }

    /**
     * Get active upcoming milestones for a child.
     */
    public function getUpcomingMilestones(Child $child): Collection
    {
        return MilestoneAlert::where('child_id', $child->id)
            ->active()
            ->upcoming()
            ->orderBy('milestone_date', 'asc')
            ->get();
    }

    /**
     * Get active upcoming milestones count for a child.
     */
    public function getUpcomingCount(Child $child): int
    {
        return $this->getUpcomingMilestones($child)->count();
    }

    /**
     * Dismiss a milestone alert.
     */
    public function dismiss(MilestoneAlert $alert): bool
    {
        return $alert->dismiss();
    }

    /**
     * Check for upcoming birthday (7 days before).
     */
    private function checkBirthday(User $user, Child $child): ?MilestoneAlert
    {
        $dob = $child->date_of_birth;
        if (! $dob) {
            return null;
        }

        $thisYearBirthday = Carbon::parse($dob)->year(now()->year);

        // If this year's birthday already passed, use next year
        if ($thisYearBirthday->isPast()) {
            $thisYearBirthday = $thisYearBirthday->addYear();
        }

        $daysUntil = (int) now()->diffInDays($thisYearBirthday, absolute: false);

        // Alert 7 days before
        if ($daysUntil > 7 || $daysUntil < 0) {
            return null;
        }

        $age = $thisYearBirthday->year - Carbon::parse($dob)->year;

        return $this->alertIfNotExists(
            user: $user,
            child: $child,
            type: MilestoneAlert::TYPE_BIRTHDAY,
            title: "Ulang Tahun {$child->name} ke-{$age}",
            description: "{$child->name} akan berulang tahun pada {$thisYearBirthday->format('d M Y')}.",
            icon: '🎂',
            alertDate: now()->toDateString(),
            milestoneDate: $thisYearBirthday->toDateString(),
        );
    }

    /**
     * Check for monthly age milestones (1, 2, 3, ... 11 months).
     */
    private function checkMonthlyAge(User $user, Child $child): ?MilestoneAlert
    {
        $dob = $child->date_of_birth;
        if (! $dob) {
            return null;
        }

        $birth = Carbon::parse($dob);
        $now = Carbon::now();

        $totalMonths = $birth->diffInMonths($now);

        // Only for months 1-11 (not 0 or 12+)
        if ($totalMonths < 1 || $totalMonths > 11) {
            return null;
        }

        // Calculate the exact month milestone date
        $milestoneDate = $birth->copy()->addMonths($totalMonths);

        // Only alert if milestone is today or within next 3 days
        $daysUntil = (int) now()->diffInDays($milestoneDate, absolute: false);
        if ($daysUntil < 0 || $daysUntil > 3) {
            return null;
        }

        return $this->alertIfNotExists(
            user: $user,
            child: $child,
            type: MilestoneAlert::TYPE_MONTHLY_AGE,
            title: "{$child->name} Genap {$totalMonths} Bulan",
            description: "{$child->name} telah genap berusia {$totalMonths} bulan pada {$milestoneDate->format('d M Y')}.",
            icon: '📅',
            alertDate: now()->toDateString(),
            milestoneDate: $milestoneDate->toDateString(),
        );
    }

    /**
     * Check for yearly age milestones (1, 2, 3, ... years).
     */
    private function checkYearlyAge(User $user, Child $child): ?MilestoneAlert
    {
        $dob = $child->date_of_birth;
        if (! $dob) {
            return null;
        }

        $birth = Carbon::parse($dob);
        $age = $birth->diffInYears(now());

        if ($age < 1) {
            return null;
        }

        $milestoneDate = $birth->copy()->addYears($age);

        // Alert 3 days before the birthday
        $daysUntil = (int) now()->diffInDays($milestoneDate, absolute: false);
        if ($daysUntil < 0 || $daysUntil > 3) {
            return null;
        }

        return $this->alertIfNotExists(
            user: $user,
            child: $child,
            type: MilestoneAlert::TYPE_YEARLY_AGE,
            title: "{$child->name} Genap {$age} Tahun",
            description: "{$child->name} telah genap berusia {$age} tahun pada {$milestoneDate->format('d M Y')}.",
            icon: '🎉',
            alertDate: now()->toDateString(),
            milestoneDate: $milestoneDate->toDateString(),
        );
    }

    /**
     * Check if growth record is due (no record in last 30 days).
     */
    private function checkGrowthRecord(User $user, Child $child): ?MilestoneAlert
    {
        $lastGrowth = $child->growths()->latest()->first();

        if ($lastGrowth) {
            $daysSinceLastRecord = now()->diffInDays($lastGrowth->created_at);

            // Only alert if more than 30 days since last record
            if ($daysSinceLastRecord < 30) {
                return null;
            }
        }

        return $this->alertIfNotExists(
            user: $user,
            child: $child,
            type: MilestoneAlert::TYPE_GROWTH_RECORD,
            title: 'Pencatatan Pertumbuhan Terlambat',
            description: "Sudah waktunya mencatat pertumbuhan {$child->name}.",
            icon: '📏',
            alertDate: now()->toDateString(),
            milestoneDate: now()->toDateString(),
        );
    }

    /**
     * Check if immunization is due (no health record with immunization type in last 60 days).
     */
    private function checkImmunization(User $user, Child $child): ?MilestoneAlert
    {
        $lastImmunization = $child->healthRecords()
            ->where('type', 'immunization')
            ->latest()
            ->first();

        if ($lastImmunization) {
            $daysSinceLastRecord = now()->diffInDays($lastImmunization->created_at);

            // Only alert if more than 60 days since last immunization record
            if ($daysSinceLastRecord < 60) {
                return null;
            }
        }

        return $this->alertIfNotExists(
            user: $user,
            child: $child,
            type: MilestoneAlert::TYPE_IMMUNIZATION,
            title: 'Pengingat Imunisasi',
            description: "Pastikan jadwal imunisasi {$child->name} sudah tercatat.",
            icon: '💉',
            alertDate: now()->toDateString(),
            milestoneDate: now()->toDateString(),
        );
    }

    /**
     * Create alert only if not already exists for this child/type/milestone_date.
     */
    private function alertIfNotExists(
        User $user,
        Child $child,
        string $type,
        string $title,
        ?string $description,
        ?string $icon,
        string $alertDate,
        string $milestoneDate,
    ): ?MilestoneAlert {
        $exists = MilestoneAlert::where('user_id', $user->id)
            ->where('child_id', $child->id)
            ->where('type', $type)
            ->whereDate('milestone_date', $milestoneDate)
            ->exists();

        if ($exists) {
            return null;
        }

        return MilestoneAlert::create([
            'user_id' => $user->id,
            'child_id' => $child->id,
            'type' => $type,
            'title' => $title,
            'description' => $description,
            'icon' => $icon,
            'alert_date' => $alertDate,
            'milestone_date' => $milestoneDate,
        ]);
    }
}
