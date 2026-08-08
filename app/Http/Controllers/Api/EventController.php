<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\StoreEventRequest;
use App\Http\Requests\Api\UpdateEventRequest;
use App\Http\Resources\EventResource;
use App\Models\Child;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventController extends ApiController
{
    /**
     * List events for a child.
     */
    public function index(Request $request, Child $child): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);

        $query = $child->events();

        if ($request->filled('date_from')) {
            $query->where('event_date', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->where('event_date', '<=', $request->input('date_to'));
        }

        if ($request->filled('event_type')) {
            $query->where('event_type', $request->input('event_type'));
        }

        $events = $query->orderBy('event_date', 'asc')
            ->paginate($request->input('per_page', 15));

        return $this->paginatedResponse($events, 'Daftar event berhasil diambil');
    }

    /**
     * Store a new event.
     */
    public function store(StoreEventRequest $request, Child $child): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);

        $data = $request->validated();
        $data['child_id'] = $child->id;
        $data['user_id'] = $request->user()->id;

        $event = Event::create($data);

        return $this->successResponse(
            new EventResource($event),
            'Event berhasil ditambahkan',
            201
        );
    }

    /**
     * Show a specific event.
     */
    public function show(Request $request, Child $child, Event $event): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);
        abort_unless($event->child_id === $child->id, 404);

        return $this->successResponse(
            new EventResource($event),
            'Detail event berhasil diambil'
        );
    }

    /**
     * Update a specific event.
     */
    public function update(UpdateEventRequest $request, Child $child, Event $event): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);
        abort_unless($event->child_id === $child->id, 404);

        $event->update($request->validated());

        return $this->successResponse(
            new EventResource($event->fresh()),
            'Event berhasil diperbarui'
        );
    }

    /**
     * Delete a specific event.
     */
    public function destroy(Request $request, Child $child, Event $event): JsonResponse
    {
        abort_if($child->user_id !== $request->user()->id, 403);
        abort_unless($event->child_id === $child->id, 404);

        $event->delete();

        return $this->successResponse(null, 'Event berhasil dihapus');
    }
}
