<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\MilestoneAlert;
use App\Services\MilestoneService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MilestoneController extends Controller
{
    /**
     * Display milestone alerts for a child.
     */
    public function index(Request $request, Child $child, MilestoneService $milestoneService): View
    {
        $milestones = $milestoneService->getUpcomingMilestones($child);

        $dismissed = MilestoneAlert::where('child_id', $child->id)
            ->where('is_dismissed', true)
            ->latest('dismissed_at')
            ->take(10)
            ->get();

        return view('milestones.index', [
            'child' => $child,
            'milestones' => $milestones,
            'dismissed' => $dismissed,
            'totalActive' => $milestones->count(),
        ]);
    }

    /**
     * Manually trigger milestone check for a child.
     */
    public function check(Request $request, Child $child, MilestoneService $milestoneService): RedirectResponse
    {
        $milestoneService->checkMilestones($request->user(), $child);

        return redirect()->route('milestones.index', $child)
            ->with('status', __('status.milestones_checked'));
    }

    /**
     * Dismiss a milestone alert.
     */
    public function dismiss(Child $child, MilestoneAlert $milestoneAlert, MilestoneService $milestoneService): RedirectResponse
    {
        abort_unless($milestoneAlert->child_id === $child->id, 403);

        $milestoneService->dismiss($milestoneAlert);

        return redirect()->route('milestones.index', $child)
            ->with('status', __('status.milestones_dismissed'));
    }
}
