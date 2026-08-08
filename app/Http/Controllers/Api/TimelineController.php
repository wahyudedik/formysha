<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\StoreTimelineRequest;
use App\Http\Requests\Api\UpdateTimelineRequest;
use App\Http\Resources\TimelineResource;
use App\Models\Child;
use App\Models\Timeline;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TimelineController extends ApiController
{
    /**
     * List timelines for a child.
     */
    public function index(Request $request, Child $child): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);

        $query = $child->timelines();

        if ($request->filled('date_from')) {
            $query->where('event_date', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->where('event_date', '<=', $request->input('date_to'));
        }

        $timelines = $query->with('media')
            ->orderBy('event_date', 'desc')
            ->paginate($request->input('per_page', 15));

        return $this->paginatedResponse($timelines, 'Daftar timeline berhasil diambil');
    }

    /**
     * Store a new timeline entry.
     */
    public function store(StoreTimelineRequest $request, Child $child): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);

        $data = $request->validated();
        $data['child_id'] = $child->id;
        $data['user_id'] = $request->user()->id;

        // Convert comma-separated tags to array
        if (isset($data['tags']) && is_string($data['tags'])) {
            $data['tags'] = array_map('trim', explode(',', $data['tags']));
        }

        $timeline = Timeline::create($data);

        return $this->successResponse(
            new TimelineResource($timeline->load('media')),
            'Timeline berhasil ditambahkan',
            201
        );
    }

    /**
     * Show a specific timeline entry.
     */
    public function show(Request $request, Child $child, Timeline $timeline): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);
        abort_unless($timeline->child_id === $child->id, 404);

        return $this->successResponse(
            new TimelineResource($timeline->load('media')),
            'Detail timeline berhasil diambil'
        );
    }

    /**
     * Update a specific timeline entry.
     */
    public function update(UpdateTimelineRequest $request, Child $child, Timeline $timeline): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);
        abort_unless($timeline->child_id === $child->id, 404);

        $data = $request->validated();

        // Convert comma-separated tags to array
        if (isset($data['tags']) && is_string($data['tags'])) {
            $data['tags'] = array_map('trim', explode(',', $data['tags']));
        }

        $timeline->update($data);

        return $this->successResponse(
            new TimelineResource($timeline->fresh()->load('media')),
            'Timeline berhasil diperbarui'
        );
    }

    /**
     * Delete a specific timeline entry.
     */
    public function destroy(Request $request, Child $child, Timeline $timeline): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);
        abort_unless($timeline->child_id === $child->id, 404);

        $timeline->delete();

        return $this->successResponse(null, 'Timeline berhasil dihapus');
    }
}
