<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\DiaryResource;
use App\Http\Resources\EventResource;
use App\Http\Resources\TimelineResource;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardApiController extends ApiController
{
    public function __construct(
        private DashboardService $dashboardService,
    ) {}

    /**
     * Return dashboard summary data for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $data = $this->dashboardService->getDashboardData($user);

        $dashboard = [
            'children_count' => $data['children']->count(),
            'children' => $data['children']->map(fn ($child) => [
                'id' => $child->id,
                'name' => $child->name,
                'slug' => $child->slug,
                'photo' => $child->photo,
                'timelines_count' => $child->timelines_count ?? 0,
                'albums_count' => $child->albums_count ?? 0,
                'diaries_count' => $child->diaries_count ?? 0,
                'documents_count' => $child->documents_count ?? 0,
                'events_count' => $child->events_count ?? 0,
                'growths_count' => $child->growths_count ?? 0,
                'health_records_count' => $child->health_records_count ?? 0,
            ]),
            'recent_timelines' => TimelineResource::collection($data['recentTimelines']),
            'upcoming_events' => EventResource::collection($data['upcomingEvents']),
            'recent_diaries' => DiaryResource::collection($data['recentDiaries']),
        ];

        return $this->successResponse($dashboard, 'Data dashboard berhasil diambil');
    }
}
