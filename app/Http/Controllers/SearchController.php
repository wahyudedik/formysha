<?php

namespace App\Http\Controllers;

use App\Models\Diary;
use App\Models\Document;
use App\Models\HealthRecord;
use App\Models\Timeline;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SearchController extends Controller
{
    /**
     * Display search results across modules.
     */
    public function index(Request $request): View
    {
        $query = $request->input('q', '');
        $module = $request->input('module', 'all');
        $results = collect();
        $counts = [
            'all' => 0,
            'timeline' => 0,
            'diary' => 0,
            'document' => 0,
            'health' => 0,
        ];

        if (strlen($query) >= 2) {
            $childIds = $request->user()->children()->pluck('id');
            $searchTerm = '%'.mb_strtolower($query).'%';

            // Search timelines
            if (in_array($module, ['all', 'timeline'])) {
                $timelines = Timeline::whereIn('child_id', $childIds)
                    ->where(function ($q) use ($searchTerm) {
                        $q->whereRaw('LOWER(title) LIKE ?', [$searchTerm])
                            ->orWhereRaw('LOWER(description) LIKE ?', [$searchTerm]);
                    })
                    ->with('child')
                    ->latest()
                    ->limit(20)
                    ->get();

                $counts['timeline'] = $timelines->count();

                if ($module === 'timeline') {
                    $results = $timelines;
                } else {
                    $results = $results->merge($timelines->map(fn ($item) => [
                        'type' => 'timeline',
                        'icon' => '📸',
                        'title' => $item->title,
                        'description' => $item->description,
                        'date' => $item->event_date?->format('d M Y'),
                        'child' => $item->child->name,
                        'url' => route('timeline.show', [$item->child, $item]),
                        'color' => 'bg-skyBlue-100 text-skyBlue-700',
                    ]));
                }
            }

            // Search diaries
            if (in_array($module, ['all', 'diary'])) {
                $diaries = Diary::whereIn('child_id', $childIds)
                    ->where(function ($q) use ($searchTerm) {
                        $q->whereRaw('LOWER(title) LIKE ?', [$searchTerm])
                            ->orWhereRaw('LOWER(content) LIKE ?', [$searchTerm]);
                    })
                    ->with('child')
                    ->latest()
                    ->limit(20)
                    ->get();

                $counts['diary'] = $diaries->count();

                if ($module === 'diary') {
                    $results = $diaries;
                } else {
                    $results = $results->merge($diaries->map(fn ($item) => [
                        'type' => 'diary',
                        'icon' => '📔',
                        'title' => $item->title,
                        'description' => Str::limit(strip_tags($item->content), 150),
                        'date' => $item->diary_date?->format('d M Y'),
                        'child' => $item->child->name,
                        'url' => route('diaries.show', [$item->child, $item]),
                        'color' => 'bg-lavender-100 text-lavender-700',
                    ]));
                }
            }

            // Search documents
            if (in_array($module, ['all', 'document'])) {
                $documents = Document::whereIn('child_id', $childIds)
                    ->where(function ($q) use ($searchTerm) {
                        $q->whereRaw('LOWER(name) LIKE ?', [$searchTerm])
                            ->orWhereRaw('LOWER(description) LIKE ?', [$searchTerm]);
                    })
                    ->with('child')
                    ->latest()
                    ->limit(20)
                    ->get();

                $counts['document'] = $documents->count();

                if ($module === 'document') {
                    $results = $documents;
                } else {
                    $results = $results->merge($documents->map(fn ($item) => [
                        'type' => 'document',
                        'icon' => '📄',
                        'title' => $item->name,
                        'description' => $item->type_label,
                        'date' => $item->issued_date?->format('d M Y'),
                        'child' => $item->child->name,
                        'url' => route('documents.show', [$item->child, $item]),
                        'color' => 'bg-warmYellow-100 text-warmYellow-700',
                    ]));
                }
            }

            // Search health records
            if (in_array($module, ['all', 'health'])) {
                $healthRecords = HealthRecord::whereIn('child_id', $childIds)
                    ->where(function ($q) use ($searchTerm) {
                        $q->whereRaw('LOWER(name) LIKE ?', [$searchTerm])
                            ->orWhereRaw('LOWER(description) LIKE ?', [$searchTerm])
                            ->orWhereRaw('LOWER(doctor) LIKE ?', [$searchTerm])
                            ->orWhereRaw('LOWER(hospital) LIKE ?', [$searchTerm]);
                    })
                    ->with('child')
                    ->latest()
                    ->limit(20)
                    ->get();

                $counts['health'] = $healthRecords->count();

                if ($module === 'health') {
                    $results = $healthRecords;
                } else {
                    $results = $results->merge($healthRecords->map(fn ($item) => [
                        'type' => 'health',
                        'icon' => $item->type_icon,
                        'title' => $item->name,
                        'description' => $item->type_label.($item->doctor ? ' — '.$item->doctor : ''),
                        'date' => $item->date?->format('d M Y'),
                        'child' => $item->child->name,
                        'url' => route('health.show', [$item->child, $item]),
                        'color' => $item->type_color,
                    ]));
                }
            }

            $counts['all'] = $counts['timeline'] + $counts['diary'] + $counts['document'] + $counts['health'];

            if ($module === 'all') {
                $results = $results->sortByDesc('date')->values();
            }
        }

        return view('search.index', [
            'query' => $query,
            'module' => $module,
            'results' => $results,
            'counts' => $counts,
        ]);
    }
}
