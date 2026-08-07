<?php

namespace App\Http\Controllers;

use App\Models\Diary;
use App\Models\Event;
use App\Models\Growth;
use App\Models\HealthRecord;
use App\Models\Timeline;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the user's dashboard with overview of all children and recent activity.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $children = $user->children()->withCount(['timelines', 'albums', 'diaries', 'documents', 'events', 'growths', 'healthRecords'])->get();

        $childIds = $children->pluck('id');

        $recentTimelines = Timeline::whereIn('child_id', $childIds)
            ->with('child')
            ->latest()
            ->take(5)
            ->get();

        $upcomingEvents = Event::whereIn('child_id', $childIds)
            ->with('child')
            ->where('event_date', '>=', now()->format('Y-m-d'))
            ->orderBy('event_date', 'asc')
            ->take(5)
            ->get();

        $recentDiaries = Diary::whereIn('child_id', $childIds)
            ->with('child')
            ->latest()
            ->take(5)
            ->get();

        $recentGrowths = Growth::whereIn('child_id', $childIds)
            ->with('child')
            ->latest()
            ->take(3)
            ->get();

        $recentHealthRecords = HealthRecord::whereIn('child_id', $childIds)
            ->with('child')
            ->latest()
            ->take(3)
            ->get();

        return view('dashboard', [
            'children' => $children,
            'recentTimelines' => $recentTimelines,
            'upcomingEvents' => $upcomingEvents,
            'recentDiaries' => $recentDiaries,
            'recentGrowths' => $recentGrowths,
            'recentHealthRecords' => $recentHealthRecords,
        ]);
    }
}
