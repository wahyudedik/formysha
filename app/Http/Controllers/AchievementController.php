<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Services\AchievementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AchievementController extends Controller
{
    public function __construct(
        private AchievementService $achievementService,
    ) {}

    /**
     * Display achievements for a child.
     */
    public function index(Request $request, Child $child): View
    {
        $achievements = $this->achievementService->getAchievements($child);
        $earnedCount = $this->achievementService->getEarnedCount($child);
        $totalCount = count($achievements);

        return view('achievements.index', [
            'child' => $child,
            'achievements' => $achievements,
            'earnedCount' => $earnedCount,
            'totalCount' => $totalCount,
        ]);
    }

    /**
     * Manually trigger achievement check for a child.
     */
    public function check(Request $request, Child $child): RedirectResponse
    {
        $newlyEarned = $this->achievementService->checkAchievements($request->user(), $child);

        $count = count($newlyEarned);

        if ($count > 0) {
            return redirect()->route('achievements.index', $child)
                ->with('status', __('status.achievements_new_count', ['count' => $count]));
        }

        return redirect()->route('achievements.index', $child)
            ->with('status', __('status.achievements_already_checked'));
    }
}
