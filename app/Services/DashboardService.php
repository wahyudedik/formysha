<?php

namespace App\Services;

use App\Models\Diary;
use App\Models\Event;
use App\Models\Growth;
use App\Models\HealthRecord;
use App\Models\Timeline;
use App\Models\User;
use Illuminate\Support\Collection;

class DashboardService
{
    /**
     * Get all dashboard data for a user.
     *
     * NOTE: We intentionally avoid caching Eloquent Collection objects directly
     * in Redis/cache because PHP 8.4 throws "incomplete object" errors during
     * unserialize() when the Collection class definition hasn't been loaded yet.
     * The dashboard queries are lightweight enough to run fresh each time.
     *
     * @return array{children: Collection, recentTimelines: Collection, upcomingEvents: Collection, recentDiaries: Collection, recentGrowths: Collection, recentHealthRecords: Collection}
     */
    public function getDashboardData(User $user): array
    {
        return $this->fetchDashboardData($user);
    }

    /**
     * Fetch fresh dashboard data from database.
     */
    protected function fetchDashboardData(User $user): array
    {
        $children = $user->children()
            ->withCount(['timelines', 'albums', 'diaries', 'documents', 'events', 'growths', 'healthRecords'])
            ->get();

        $childIds = $children->pluck('id');

        $recentTimelines = $this->getRecentTimelines($childIds);
        $upcomingEvents = $this->getUpcomingEvents($childIds);
        $recentDiaries = $this->getRecentDiaries($childIds);
        $recentGrowths = $this->getRecentGrowths($childIds);
        $recentHealthRecords = $this->getRecentHealthRecords($childIds);

        return compact('children', 'recentTimelines', 'upcomingEvents', 'recentDiaries', 'recentGrowths', 'recentHealthRecords');
    }

    /**
     * Get recent timeline entries across all children.
     */
    protected function getRecentTimelines(Collection $childIds, int $limit = 5): Collection
    {
        return Timeline::whereIn('child_id', $childIds)
            ->with('child')
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * Get upcoming events across all children.
     */
    protected function getUpcomingEvents(Collection $childIds, int $limit = 5): Collection
    {
        return Event::whereIn('child_id', $childIds)
            ->with('child')
            ->where('event_date', '>=', now()->format('Y-m-d'))
            ->orderBy('event_date', 'asc')
            ->take($limit)
            ->get();
    }

    /**
     * Get recent diary entries across all children.
     */
    protected function getRecentDiaries(Collection $childIds, int $limit = 5): Collection
    {
        return Diary::whereIn('child_id', $childIds)
            ->with('child')
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * Get recent growth records across all children.
     */
    protected function getRecentGrowths(Collection $childIds, int $limit = 3): Collection
    {
        return Growth::whereIn('child_id', $childIds)
            ->with('child')
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * Get recent health records across all children.
     */
    protected function getRecentHealthRecords(Collection $childIds, int $limit = 3): Collection
    {
        return HealthRecord::whereIn('child_id', $childIds)
            ->with('child')
            ->latest()
            ->take($limit)
            ->get();
    }
}
