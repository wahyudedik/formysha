<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTimelineRequest;
use App\Http\Requests\UpdateTimelineRequest;
use App\Models\Child;
use App\Models\Timeline;
use App\Services\MediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TimelineController extends Controller
{
    /**
     * Display a listing of timeline entries for a child.
     */
    public function index(Request $request, Child $child): View
    {
        $query = $child->timelines()->with('media');

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('event_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('event_date', '<=', $request->date_to);
        }

        // Filter by tag
        if ($request->filled('tag')) {
            $query->whereJsonContains('tags', $request->tag);
        }

        // Sort options
        $sort = $request->input('sort', 'newest');
        match ($sort) {
            'oldest' => $query->orderBy('event_date', 'asc')->orderBy('event_time', 'asc'),
            'title_asc' => $query->orderBy('title', 'asc'),
            'title_desc' => $query->orderBy('title', 'desc'),
            default => $query->orderBy('event_date', 'desc')->orderBy('event_time', 'desc'),
        };

        $timelines = $query->paginate(12)->withQueryString();

        return view('timeline.index', [
            'child' => $child,
            'timelines' => $timelines,
            'currentSort' => $sort,
            'request' => $request,
        ]);
    }

    /**
     * Show the form for creating a new timeline entry.
     */
    public function create(Request $request, Child $child): View
    {
        $children = $request->user()->children()->get();

        return view('timeline.create', [
            'child' => $child,
            'children' => $children,
        ]);
    }

    /**
     * Store a newly created timeline entry in storage.
     */
    public function store(StoreTimelineRequest $request, Child $child): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        // Remove media from data before creating timeline
        $mediaFiles = $request->file('media') ?? [];
        unset($data['media']);

        $timeline = $child->timelines()->create($data);

        // Handle media upload
        if (! empty($mediaFiles)) {
            $mediaService = new MediaService;
            $mediaService->uploadMultiple($mediaFiles, $timeline);
        }

        return redirect()->route('timeline.index', $child)
            ->with('status', 'Kenangan berhasil ditambahkan!');
    }

    /**
     * Display the specified timeline entry.
     */
    public function show(Request $request, Child $child, Timeline $timeline): View
    {
        abort_unless($timeline->child_id === $child->id, 403);

        $timeline->load('media');

        return view('timeline.show', [
            'child' => $child,
            'timeline' => $timeline,
        ]);
    }

    /**
     * Show the form for editing the specified timeline entry.
     */
    public function edit(Request $request, Child $child, Timeline $timeline): View
    {
        abort_unless($timeline->child_id === $child->id, 403);

        return view('timeline.edit', [
            'child' => $child,
            'timeline' => $timeline,
        ]);
    }

    /**
     * Update the specified timeline entry in storage.
     */
    public function update(UpdateTimelineRequest $request, Child $child, Timeline $timeline): RedirectResponse
    {
        abort_unless($timeline->child_id === $child->id, 403);

        $timeline->update($request->validated());

        return redirect()->route('timeline.show', [$child, $timeline])
            ->with('status', 'Kenangan berhasil diperbarui!');
    }

    /**
     * Remove the specified timeline entry from storage.
     */
    public function destroy(Request $request, Child $child, Timeline $timeline): RedirectResponse
    {
        abort_unless($timeline->child_id === $child->id, 403);

        $timeline->delete();

        return redirect()->route('timeline.index', $child)
            ->with('status', 'Kenangan berhasil dihapus.');
    }
}
