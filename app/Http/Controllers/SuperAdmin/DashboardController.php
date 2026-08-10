<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\ClinicalNote;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Referral;
use App\Models\Staff;
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

        // ─── B2B Statistics ──────────────────────────────────────

        $b2bTenantCount = Tenant::where('type', '!=', 'family')->count();
        $b2bActiveCount = Tenant::where('type', '!=', 'family')
            ->where('is_active', true)
            ->count();
        $totalStaff = Staff::where('is_active', true)->count();
        $totalPatientLinks = (new Tenant)->patientLinks()->count();
        $clinicalNotesThisMonth = ClinicalNote::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $pendingReferrals = Referral::where('status', 'pending')->count();

        // B2B revenue
        $b2bTenantIds = Tenant::where('type', '!=', 'family')->pluck('id');
        $revenueB2B = Payment::where('status', 'approved')
            ->whereIn('tenant_id', $b2bTenantIds)
            ->sum('amount');
        $revenueB2C = $revenueTotal - $revenueB2B;

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
            'b2bTenantCount',
            'b2bActiveCount',
            'totalStaff',
            'totalPatientLinks',
            'clinicalNotesThisMonth',
            'pendingReferrals',
            'revenueB2B',
            'revenueB2C',
        ));
    }
}
