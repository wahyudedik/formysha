<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\DiaryResource;
use App\Http\Resources\DocumentResource;
use App\Http\Resources\EventResource;
use App\Http\Resources\TimelineResource;
use App\Models\Diary;
use App\Models\Document;
use App\Models\Event;
use App\Models\Timeline;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends ApiController
{
    /**
     * Global search across timelines, diaries, documents, and events.
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'q' => ['required', 'string', 'min:2'],
            'type' => ['nullable', 'string', 'in:all,timeline,diary,document,event'],
        ]);

        $query = $request->input('q');
        $type = $request->input('type', 'all');
        $userId = $request->user()->id;

        // Get child IDs for the authenticated user
        $childIds = $request->user()->children()->pluck('id');

        $results = [];

        if ($type === 'all' || $type === 'timeline') {
            $results['timelines'] = TimelineResource::collection(
                Timeline::whereIn('child_id', $childIds)
                    ->where(function ($q) use ($query) {
                        $q->where('title', 'like', "%{$query}%")
                            ->orWhere('description', 'like', "%{$query}%");
                    })
                    ->latest()
                    ->take(20)
                    ->get()
            );
        }

        if ($type === 'all' || $type === 'diary') {
            $results['diaries'] = DiaryResource::collection(
                Diary::whereIn('child_id', $childIds)
                    ->where(function ($q) use ($query) {
                        $q->where('title', 'like', "%{$query}%")
                            ->orWhere('content', 'like', "%{$query}%");
                    })
                    ->latest()
                    ->take(20)
                    ->get()
            );
        }

        if ($type === 'all' || $type === 'document') {
            $results['documents'] = DocumentResource::collection(
                Document::whereIn('child_id', $childIds)
                    ->where(function ($q) use ($query) {
                        $q->where('name', 'like', "%{$query}%")
                            ->orWhere('type', 'like', "%{$query}%");
                    })
                    ->latest()
                    ->take(20)
                    ->get()
            );
        }

        if ($type === 'all' || $type === 'event') {
            $results['events'] = EventResource::collection(
                Event::whereIn('child_id', $childIds)
                    ->where(function ($q) use ($query) {
                        $q->where('title', 'like', "%{$query}%")
                            ->orWhere('description', 'like', "%{$query}%");
                    })
                    ->latest()
                    ->take(20)
                    ->get()
            );
        }

        return $this->successResponse($results, 'Pencarian berhasil');
    }
}
