<?php

namespace App\Services;

use App\Models\Album;
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
     * Get all dashboard data for a user, optionally filtered by a specific child.
     *
     * NOTE: We intentionally avoid caching Eloquent Collection objects directly
     * in Redis/cache because PHP 8.4 throws "incomplete object" errors during
     * unserialize() when the Collection class definition hasn't been loaded yet.
     * The dashboard queries are lightweight enough to run fresh each time.
     *
     * @return array{children: Collection, selectedChild: ?Child, recentTimelines: Collection, upcomingEvents: Collection, recentDiaries: Collection, recentGrowths: Collection, recentHealthRecords: Collection, recentMedia: Collection, totalMediaCount: int, totalDocumentCount: int}
     */
    public function getDashboardData(User $user, ?string $childSlug = null): array
    {
        return $this->fetchDashboardData($user, $childSlug);
    }

    /**
     * Fetch fresh dashboard data from database.
     */
    protected function fetchDashboardData(User $user, ?string $childSlug = null): array
    {
        $children = $user->children()
            ->withCount(['timelines', 'albums', 'diaries', 'documents', 'events', 'growths', 'healthRecords'])
            ->get();

        $selectedChild = null;
        if ($childSlug && $children->contains('slug', $childSlug)) {
            $selectedChild = $children->firstWhere('slug', $childSlug);
        }

        $childIds = $selectedChild ? collect([$selectedChild->id]) : $children->pluck('id');

        $recentTimelines = $this->getRecentTimelines($childIds);
        $upcomingEvents = $this->getUpcomingEvents($childIds);
        $recentDiaries = $this->getRecentDiaries($childIds);
        $recentGrowths = $this->getRecentGrowths($childIds);
        $recentHealthRecords = $this->getRecentHealthRecords($childIds);
        $recentMedia = $this->getRecentMedia($childIds);
        $totalMediaCount = $this->getTotalMediaCount($childIds);

        $totalDocumentCount = $selectedChild
            ? (int) $selectedChild->documents_count
            : (int) $children->sum('documents_count');

        return compact(
            'children',
            'selectedChild',
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
     *
     * Includes media attached directly to Child, as well as media attached
     * to Timeline, Album, and Diary records owned by the children.
     */
    protected function getRecentMedia(Collection $childIds, int $limit = 8): Collection
    {
        if ($childIds->isEmpty()) {
            return collect();
        }

        // Get all related model IDs owned by these children
        $timelineIds = Timeline::whereIn('child_id', $childIds)->pluck('id');
        $albumIds = Album::whereIn('child_id', $childIds)->pluck('id');
        $diaryIds = Diary::whereIn('child_id', $childIds)->pluck('id');

        return Media::where('file_type', 'photo')
            ->where(function ($query) use ($childIds, $timelineIds, $albumIds, $diaryIds) {
                $query->where(fn ($q) => $q->where('mediable_type', Child::class)->whereIn('mediable_id', $childIds))
                    ->orWhere(fn ($q) => $q->where('mediable_type', Timeline::class)->whereIn('mediable_id', $timelineIds))
                    ->orWhere(fn ($q) => $q->where('mediable_type', Album::class)->whereIn('mediable_id', $albumIds))
                    ->orWhere(fn ($q) => $q->where('mediable_type', Diary::class)->whereIn('mediable_id', $diaryIds));
            })
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * Get total media count across all children.
     *
     * Includes media attached to Child, Timeline, Album, and Diary.
     */
    protected function getTotalMediaCount(Collection $childIds): int
    {
        if ($childIds->isEmpty()) {
            return 0;
        }

        $timelineIds = Timeline::whereIn('child_id', $childIds)->pluck('id');
        $albumIds = Album::whereIn('child_id', $childIds)->pluck('id');
        $diaryIds = Diary::whereIn('child_id', $childIds)->pluck('id');

        return Media::where(function ($query) use ($childIds, $timelineIds, $albumIds, $diaryIds) {
            $query->where(fn ($q) => $q->where('mediable_type', Child::class)->whereIn('mediable_id', $childIds))
                ->orWhere(fn ($q) => $q->where('mediable_type', Timeline::class)->whereIn('mediable_id', $timelineIds))
                ->orWhere(fn ($q) => $q->where('mediable_type', Album::class)->whereIn('mediable_id', $albumIds))
                ->orWhere(fn ($q) => $q->where('mediable_type', Diary::class)->whereIn('mediable_id', $diaryIds));
        })->count();
    }
}
