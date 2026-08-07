<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\Payment;
use App\Models\Plan;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the super admin dashboard.
     */
    public function __invoke(Request $request): View
    {
        $totalTenants = Tenant::count();
        $activeTenants = Tenant::where('is_active', true)->count();
        $totalPlans = Plan::count();

        $totalPayments = Payment::count();
        $pendingPayments = Payment::where('status', 'pending')->count();
        $approvedPayments = Payment::where('status', 'approved')->count();
        $rejectedPayments = Payment::where('status', 'rejected')->count();

        $revenueThisMonth = Payment::where('status', 'approved')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        $revenueTotal = Payment::where('status', 'approved')
            ->sum('amount');

        $recentPayments = Payment::with(['tenant', 'subscription.plan'])
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        $recentTenants = Tenant::withCount('users', 'children')
            ->latest()
            ->take(5)
            ->get();

        return view('super-admin.dashboard', compact(
            'totalTenants',
            'activeTenants',
            'totalPlans',
            'totalPayments',
            'pendingPayments',
            'approvedPayments',
            'rejectedPayments',
            'revenueThisMonth',
            'revenueTotal',
            'recentPayments',
            'recentTenants',
        ));
    }
}
