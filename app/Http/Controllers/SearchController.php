<?php

namespace App\Http\Controllers;

use App\Services\SearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function __construct(
        private SearchService $searchService,
    ) {}

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
            'album' => 0,
            'diary' => 0,
            'document' => 0,
            'health' => 0,
            'growth' => 0,
        ];

        if (mb_strlen($query) >= SearchService::MIN_QUERY_LENGTH) {
            $searchResults = $this->searchService->search($request->user(), $query, $module);

            // Map timelines
            if (in_array($module, ['all', 'timeline'])) {
                $timelines = $searchResults['timelines'];

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

            // Map albums
            if (in_array($module, ['all', 'album'])) {
                $albums = $searchResults['albums'];

                $counts['album'] = $albums->count();

                if ($module === 'album') {
                    $results = $albums;
                } else {
                    $results = $results->merge($albums->map(fn ($item) => [
                        'type' => 'album',
                        'icon' => '🖼️',
                        'title' => $item->name,
                        'description' => $item->description,
                        'date' => $item->created_at?->format('d M Y'),
                        'child' => $item->child->name,
                        'url' => route('albums.show', [$item->child, $item]),
                        'color' => 'bg-peach-100 text-peach-700',
                    ]));
                }
            }

            // Map diaries
            if (in_array($module, ['all', 'diary'])) {
                $diaries = $searchResults['diaries'];

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

            // Map documents
            if (in_array($module, ['all', 'document'])) {
                $documents = $searchResults['documents'];

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

            // Map health records
            if (in_array($module, ['all', 'health'])) {
                $healthRecords = $searchResults['health'];

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

            // Map growth records
            if (in_array($module, ['all', 'growth'])) {
                $growths = $searchResults['growths'];

                $counts['growth'] = $growths->count();

                if ($module === 'growth') {
                    $results = $growths;
                } else {
                    $results = $results->merge($growths->map(fn ($item) => [
                        'type' => 'growth',
                        'icon' => '📏',
                        'title' => 'Pertumbuhan — '.$item->child->name,
                        'description' => trim(($item->weight_label ? 'Berat: '.$item->weight_label : '').' '.($item->height_label ? 'Tinggi: '.$item->height_label : '').' '.($item->head_circumference_label ? 'Lingkar Kepala: '.$item->head_circumference_label : '')),
                        'date' => $item->date?->format('d M Y'),
                        'child' => $item->child->name,
                        'url' => route('growth.index', $item->child),
                        'color' => 'bg-mintGreen-100 text-mintGreen-700',
                    ]));
                }
            }

            $counts['all'] = $counts['timeline'] + $counts['album'] + $counts['diary'] + $counts['document'] + $counts['health'] + $counts['growth'];

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
