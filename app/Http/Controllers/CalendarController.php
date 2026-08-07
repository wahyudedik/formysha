<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Child;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalendarController extends Controller
{
    /**
     * Display a listing of events for a child.
     */
    public function index(Request $request, Child $child): View
    {
        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);

        $events = $child->events()
            ->whereYear('event_date', $year)
            ->whereMonth('event_date', $month)
            ->orderBy('event_date', 'asc')
            ->orderBy('event_time', 'asc')
            ->paginate(15)
            ->withQueryString();

        $allMonthEvents = $child->events()
            ->whereYear('event_date', $year)
            ->whereMonth('event_date', $month)
            ->orderBy('event_date', 'asc')
            ->get();

        return view('calendar.index', [
            'child' => $child,
            'events' => $events,
            'allMonthEvents' => $allMonthEvents,
            'currentMonth' => $month,
            'currentYear' => $year,
        ]);
    }

    /**
     * Show the form for creating a new event.
     */
    public function create(Request $request, Child $child): View
    {
        $children = $request->user()->children()->get();

        return view('calendar.create', [
            'child' => $child,
            'children' => $children,
        ]);
    }

    /**
     * Store a newly created event in storage.
     */
    public function store(StoreEventRequest $request, Child $child): RedirectResponse
    {
        $validated = $request->validated();
        $validated['user_id'] = $request->user()->id;

        $child->events()->create($validated);

        return redirect()->route('calendar.index', $child)
            ->with('status', 'Acara berhasil disimpan!');
    }

    /**
     * Display the specified event.
     */
    public function show(Request $request, Child $child, Event $event): View
    {
        abort_unless($event->child_id === $child->id, 403);

        return view('calendar.show', [
            'child' => $child,
            'event' => $event,
        ]);
    }

    /**
     * Show the form for editing the specified event.
     */
    public function edit(Request $request, Child $child, Event $event): View
    {
        abort_unless($event->child_id === $child->id, 403);

        return view('calendar.edit', [
            'child' => $child,
            'event' => $event,
        ]);
    }

    /**
     * Update the specified event in storage.
     */
    public function update(UpdateEventRequest $request, Child $child, Event $event): RedirectResponse
    {
        abort_unless($event->child_id === $child->id, 403);

        $event->update($request->validated());

        return redirect()->route('calendar.show', [$child, $event])
            ->with('status', 'Acara berhasil diperbarui!');
    }

    /**
     * Remove the specified event from storage.
     */
    public function destroy(Request $request, Child $child, Event $event): RedirectResponse
    {
        abort_unless($event->child_id === $child->id, 403);

        $event->delete();

        return redirect()->route('calendar.index', $child)
            ->with('status', 'Acara berhasil dihapus.');
    }
}
