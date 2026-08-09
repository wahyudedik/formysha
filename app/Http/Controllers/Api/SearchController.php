<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ChildResource;
use App\Http\Resources\DiaryResource;
use App\Http\Resources\DocumentResource;
use App\Http\Resources\EventResource;
use App\Http\Resources\FamilyMemberResource;
use App\Http\Resources\GrowthResource;
use App\Http\Resources\HealthRecordResource;
use App\Http\Resources\TimelineResource;
use App\Services\SearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends ApiController
{
    public function __construct(
        private SearchService $searchService,
    ) {}

    /**
     * Global search across all modules.
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'q' => ['required', 'string', 'min:'.SearchService::MIN_QUERY_LENGTH],
            'type' => ['nullable', 'string', 'in:'.implode(',', array_merge(['all'], SearchService::TYPES))],
        ]);

        $query = $request->input('q');
        $type = $request->input('type', 'all');

        $searchResults = $this->searchService->search($request->user(), $query, $type);

        $results = [];

        if ($type === 'all' || $type === 'child') {
            $results['children'] = ChildResource::collection($searchResults['children']);
        }

        if ($type === 'all' || $type === 'timeline') {
            $results['timelines'] = TimelineResource::collection($searchResults['timelines']);
        }

        if ($type === 'all' || $type === 'diary') {
            $results['diaries'] = DiaryResource::collection($searchResults['diaries']);
        }

        if ($type === 'all' || $type === 'document') {
            $results['documents'] = DocumentResource::collection($searchResults['documents']);
        }

        if ($type === 'all' || $type === 'event') {
            $results['events'] = EventResource::collection($searchResults['events']);
        }

        if ($type === 'all' || $type === 'health') {
            $results['health'] = HealthRecordResource::collection($searchResults['health']);
        }

        if ($type === 'all' || $type === 'growth') {
            $results['growths'] = GrowthResource::collection($searchResults['growths']);
        }

        if ($type === 'all' || $type === 'family') {
            $results['families'] = FamilyMemberResource::collection($searchResults['families']);
        }

        $results['counts'] = $searchResults['counts'];

        return $this->successResponse($results, 'Pencarian berhasil');
    }
}
