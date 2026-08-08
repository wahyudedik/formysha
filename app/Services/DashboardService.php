<?php

namespace App\Services;

use App\Models\Child;
use App\Models\Diary;
use App\Models\Event;
use App\Models\Growth;
use App\Models\HealthRecord;
use App\Models\Media;
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
     * @return array{children: Collection, recentTimelines: Collection, upcomingEvents: Collection, recentDiaries: Collection, recentGrowths: Collection, recentHealthRecords: Collection, recentMedia: Collection, totalMediaCount: int, totalDocumentCount: int}
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
        $recentMedia = $this->getRecentMedia($childIds);
        $totalMediaCount = $this->getTotalMediaCount($childIds);
        $totalDocumentCount = (int) $children->sum('documents_count');

        return compact(
            'children',
            'recentTimelines',
            'upcomingEvents',
            'recentDiaries',
            'recentGrowths',
            'recentHealthRecords',
            'recentMedia',
            'totalMediaCount',
            'totalDocumentCount',
        );
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

    /**
     * Get recent media (photos/videos) across all children for dashboard thumbnails.
     */
    protected function getRecentMedia(Collection $childIds, int $limit = 8): Collection
    {
        if ($childIds->isEmpty()) {
            return collect();
        }

        return Media::where('mediable_type', Child::class)
            ->whereIn('mediable_id', $childIds)
            ->where('file_type', 'photo')
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * Get total media count across all children.
     */
    protected function getTotalMediaCount(Collection $childIds): int
    {
        if ($childIds->isEmpty()) {
            return 0;
        }

        return Media::where('mediable_type', Child::class)
            ->whereIn('mediable_id', $childIds)
            ->count();
    }
}
