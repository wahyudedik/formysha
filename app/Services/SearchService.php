<?php

namespace App\Services;

use App\Models\Album;
use App\Models\Child;
use App\Models\Diary;
use App\Models\Document;
use App\Models\Event;
use App\Models\FamilyMember;
use App\Models\Growth;
use App\Models\HealthRecord;
use App\Models\Timeline;
use App\Models\User;
use Illuminate\Support\Collection;

class SearchService
{
    /**
     * Available search types.
     *
     * @var list<string>
     */
    public const TYPES = ['child', 'timeline', 'album', 'diary', 'document', 'event', 'health', 'growth', 'family'];

    /**
     * Maximum results per type.
     */
    public const MAX_RESULTS = 20;

    /**
     * Minimum query length.
     */
    public const MIN_QUERY_LENGTH = 2;

    /**
     * Search across all modules for a user.
     *
     * @return array{children: Collection, timelines: Collection, diaries: Collection, documents: Collection, events: Collection, health: Collection, growths: Collection, families: Collection, counts: array<string, int>}
     */
    public function search(User $user, string $query, string $type = 'all'): array
    {
        $childIds = $user->children()->pluck('id');
        $searchTerm = '%'.mb_strtolower($query).'%';

        $results = [
            'children' => collect(),
            'timelines' => collect(),
            'albums' => collect(),
            'diaries' => collect(),
            'documents' => collect(),
            'events' => collect(),
            'health' => collect(),
            'growths' => collect(),
            'families' => collect(),
        ];

        $counts = [
            'all' => 0,
            'child' => 0,
            'timeline' => 0,
            'album' => 0,
            'diary' => 0,
            'document' => 0,
            'event' => 0,
            'health' => 0,
            'growth' => 0,
            'family' => 0,
        ];

        if (in_array($type, ['all', 'child'])) {
            $results['children'] = $this->searchChildren($user, $searchTerm);
            $counts['child'] = $results['children']->count();
        }

        if (in_array($type, ['all', 'timeline'])) {
            $results['timelines'] = $this->searchTimelines($childIds, $searchTerm);
            $counts['timeline'] = $results['timelines']->count();
        }

        if (in_array($type, ['all', 'album'])) {
            $results['albums'] = $this->searchAlbums($childIds, $searchTerm);
            $counts['album'] = $results['albums']->count();
        }

        if (in_array($type, ['all', 'diary'])) {
            $results['diaries'] = $this->searchDiaries($childIds, $searchTerm);
            $counts['diary'] = $results['diaries']->count();
        }

        if (in_array($type, ['all', 'document'])) {
            $results['documents'] = $this->searchDocuments($childIds, $searchTerm);
            $counts['document'] = $results['documents']->count();
        }

        if (in_array($type, ['all', 'event'])) {
            $results['events'] = $this->searchEvents($childIds, $searchTerm);
            $counts['event'] = $results['events']->count();
        }

        if (in_array($type, ['all', 'health'])) {
            $results['health'] = $this->searchHealthRecords($childIds, $searchTerm);
            $counts['health'] = $results['health']->count();
        }

        if (in_array($type, ['all', 'growth'])) {
            $results['growths'] = $this->searchGrowths($childIds, $searchTerm);
            $counts['growth'] = $results['growths']->count();
        }

        if (in_array($type, ['all', 'family'])) {
            $results['families'] = $this->searchFamilyMembers($childIds, $searchTerm);
            $counts['family'] = $results['families']->count();
        }

        $counts['all'] = array_sum(array_filter($counts, fn ($key) => $key !== 'all', ARRAY_FILTER_USE_KEY));

        return [
            'children' => $results['children'],
            'timelines' => $results['timelines'],
            'albums' => $results['albums'],
            'diaries' => $results['diaries'],
            'documents' => $results['documents'],
            'events' => $results['events'],
            'health' => $results['health'],
            'growths' => $results['growths'],
            'families' => $results['families'],
            'counts' => $counts,
        ];
    }

    /**
     * Search children by name, nickname, or bio.
     */
    public function searchChildren(User $user, string $searchTerm): Collection
    {
        return Child::where('user_id', $user->id)
            ->where(function ($q) use ($searchTerm) {
                $q->whereRaw('LOWER(name) LIKE ?', [$searchTerm])
                    ->orWhereRaw('LOWER(nickname) LIKE ?', [$searchTerm])
                    ->orWhereRaw('LOWER(bio) LIKE ?', [$searchTerm]);
            })
            ->latest()
            ->limit(self::MAX_RESULTS)
            ->get();
    }

    /**
     * Search timelines by title or description.
     */
    public function searchTimelines(Collection $childIds, string $searchTerm): Collection
    {
        return Timeline::whereIn('child_id', $childIds)
            ->where(function ($q) use ($searchTerm) {
                $q->whereRaw('LOWER(title) LIKE ?', [$searchTerm])
                    ->orWhereRaw('LOWER(description) LIKE ?', [$searchTerm]);
            })
            ->with('child')
            ->latest()
            ->limit(self::MAX_RESULTS)
            ->get();
    }

    /**
     * Search diaries by title or content.
     */
    public function searchDiaries(Collection $childIds, string $searchTerm): Collection
    {
        return Diary::whereIn('child_id', $childIds)
            ->where(function ($q) use ($searchTerm) {
                $q->whereRaw('LOWER(title) LIKE ?', [$searchTerm])
                    ->orWhereRaw('LOWER(content) LIKE ?', [$searchTerm]);
            })
            ->with('child')
            ->latest()
            ->limit(self::MAX_RESULTS)
            ->get();
    }

    /**
     * Search documents by name, type, or description.
     */
    public function searchDocuments(Collection $childIds, string $searchTerm): Collection
    {
        return Document::whereIn('child_id', $childIds)
            ->where(function ($q) use ($searchTerm) {
                $q->whereRaw('LOWER(name) LIKE ?', [$searchTerm])
                    ->orWhereRaw('LOWER(type) LIKE ?', [$searchTerm])
                    ->orWhereRaw('LOWER(description) LIKE ?', [$searchTerm]);
            })
            ->with('child')
            ->latest()
            ->limit(self::MAX_RESULTS)
            ->get();
    }

    /**
     * Search events by title or description.
     */
    public function searchEvents(Collection $childIds, string $searchTerm): Collection
    {
        return Event::whereIn('child_id', $childIds)
            ->where(function ($q) use ($searchTerm) {
                $q->whereRaw('LOWER(title) LIKE ?', [$searchTerm])
                    ->orWhereRaw('LOWER(description) LIKE ?', [$searchTerm]);
            })
            ->with('child')
            ->latest()
            ->limit(self::MAX_RESULTS)
            ->get();
    }

    /**
     * Search health records by name, description, doctor, or hospital.
     */
    public function searchHealthRecords(Collection $childIds, string $searchTerm): Collection
    {
        return HealthRecord::whereIn('child_id', $childIds)
            ->where(function ($q) use ($searchTerm) {
                $q->whereRaw('LOWER(name) LIKE ?', [$searchTerm])
                    ->orWhereRaw('LOWER(description) LIKE ?', [$searchTerm])
                    ->orWhereRaw('LOWER(doctor) LIKE ?', [$searchTerm])
                    ->orWhereRaw('LOWER(hospital) LIKE ?', [$searchTerm]);
            })
            ->with('child')
            ->latest()
            ->limit(self::MAX_RESULTS)
            ->get();
    }

    /**
     * Search growth records by notes.
     */
    public function searchGrowths(Collection $childIds, string $searchTerm): Collection
    {
        return Growth::whereIn('child_id', $childIds)
            ->where(function ($q) use ($searchTerm) {
                $q->whereRaw('LOWER(notes) LIKE ?', [$searchTerm]);
            })
            ->with('child')
            ->latest()
            ->limit(self::MAX_RESULTS)
            ->get();
    }

    /**
     * Search albums by name or description.
     */
    public function searchAlbums(Collection $childIds, string $searchTerm): Collection
    {
        return Album::whereIn('child_id', $childIds)
            ->where(function ($q) use ($searchTerm) {
                $q->whereRaw('LOWER(name) LIKE ?', [$searchTerm])
                    ->orWhereRaw('LOWER(description) LIKE ?', [$searchTerm]);
            })
            ->with('child')
            ->latest()
            ->limit(self::MAX_RESULTS)
            ->get();
    }

    /**
     * Search family members by name or relationship.
     */
    public function searchFamilyMembers(Collection $childIds, string $searchTerm): Collection
    {
        return FamilyMember::whereIn('child_id', $childIds)
            ->where(function ($q) use ($searchTerm) {
                $q->whereRaw('LOWER(name) LIKE ?', [$searchTerm])
                    ->orWhereRaw('LOWER(relationship) LIKE ?', [$searchTerm]);
            })
            ->with('child')
            ->latest()
            ->limit(self::MAX_RESULTS)
            ->get();
    }
}
