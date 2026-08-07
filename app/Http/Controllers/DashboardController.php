<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private DashboardService $dashboardService,
    ) {}

    /**
     * Display the user's dashboard with overview of all children and recent activity.
     */
    public function index(Request $request): View
    {
        $data = $this->dashboardService->getDashboardData($request->user());

        return view('dashboard', $data);
    }
}
