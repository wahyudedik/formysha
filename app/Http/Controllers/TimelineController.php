<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTimelineRequest;
use App\Http\Requests\UpdateTimelineRequest;
use App\Models\Child;
use App\Models\Timeline;
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
        $timelines = $child->timelines()
            ->with('media')
            ->orderBy('event_date', 'desc')
            ->orderBy('event_time', 'desc')
            ->paginate(12);

        return view('timeline.index', [
            'child' => $child,
            'timelines' => $timelines,
        ]);
    }

    /**
     * Show the form for creating a new timeline entry.
     */
    public function create(Request $request, Child $child): View
    {
        return view('timeline.create', [
            'child' => $child,
        ]);
    }

    /**
     * Store a newly created timeline entry in storage.
     */
    public function store(StoreTimelineRequest $request, Child $child): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        $child->timelines()->create($data);

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
