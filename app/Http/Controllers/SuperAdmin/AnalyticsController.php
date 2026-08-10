<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\ClinicalNote;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Referral;
use App\Models\Staff;
use App\Models\Subscription;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    /**
     * Tampilkan halaman analytics Super Admin.
     */
    public function index(Request $request): View
    {
        // Total tenant
        $totalTenants = Tenant::count();
        $activeTenants = Tenant::where('is_active', true)->count();

        // Revenue
        $revenueThisMonth = Payment::where('status', 'approved')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        $revenueLastMonth = Payment::where('status', 'approved')
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('amount');

        $revenueTotal = Payment::where('status', 'approved')
            ->sum('amount');

        // New tenants per month (6 bulan terakhir)
        $newTenantsPerMonth = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $newTenantsPerMonth[] = [
                'month' => $date->locale('id')->isoFormat('MMM YYYY'),
                'count' => Tenant::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count(),
            ];
        }

        // Revenue per month (6 bulan terakhir)
        $revenuePerMonth = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $revenuePerMonth[] = [
                'month' => $date->locale('id')->isoFormat('MMM YYYY'),
                'amount' => Payment::where('status', 'approved')
                    ->whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->sum('amount'),
            ];
        }

        // Subscription distribution
        $subscriptionDistribution = [
            ['status' => 'Aktif', 'count' => Subscription::where('status', 'active')->count()],
            ['status' => 'Pending', 'count' => Subscription::where('status', 'pending')->count()],
            ['status' => 'Tidak Aktif', 'count' => Subscription::where('status', 'inactive')->count()],
            ['status' => 'Dibatalkan', 'count' => Subscription::where('status', 'cancelled')->count()],
            ['status' => 'Jatuh Tempo', 'count' => Subscription::where('status', 'past_due')->count()],
        ];

        // Top plans by subscriber count
        $topPlans = Plan::withCount('subscriptions')
            ->orderByDesc('subscriptions_count')
            ->take(5)
            ->get();

        // Revenue by plan
        $revenueByPlan = Plan::select('plans.id', 'plans.name')
            ->join('subscriptions', 'plans.id', '=', 'subscriptions.plan_id')
            ->join('payments', 'subscriptions.id', '=', 'payments.subscription_id')
            ->where('payments.status', 'approved')
            ->groupBy('plans.id', 'plans.name')
            ->selectRaw('plans.name, SUM(payments.amount) as total_revenue')
            ->orderByDesc('total_revenue')
            ->get();

        // Churn rate (subscriptions cancelled in last 30 days vs total active)
        $cancelledLast30Days = Subscription::where('status', 'cancelled')
            ->where('cancelled_at', '>=', now()->subDays(30))
            ->count();
        $totalActive = Subscription::where('status', 'active')->count();
        $churnRate = $totalActive > 0
            ? round(($cancelledLast30Days / $totalActive) * 100, 1)
            : 0;

        // ─── B2B Analytics ───────────────────────────────────────

        $b2bTenantIds = Tenant::where('type', '!=', 'family')->pluck('id');

        // B2B tenants per month (6 bulan terakhir)
        $b2bTenantsPerMonth = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $b2bTenantsPerMonth[] = [
                'month' => $date->locale('id')->isoFormat('MMM YYYY'),
                'count' => Tenant::where('type', '!=', 'family')
                    ->whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count(),
            ];
        }

        // Clinical notes per month (6 bulan terakhir)
        $clinicalNotesPerMonth = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $clinicalNotesPerMonth[] = [
                'month' => $date->locale('id')->isoFormat('MMM YYYY'),
                'count' => ClinicalNote::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count(),
            ];
        }

        // Referrals per month (6 bulan terakhir)
        $referralsPerMonth = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $referralsPerMonth[] = [
                'month' => $date->locale('id')->isoFormat('MMM YYYY'),
                'count' => Referral::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count(),
            ];
        }

        // Top facilities by staff count
        $topFacilities = Tenant::where('type', '!=', 'family')
            ->withCount('staff')
            ->orderByDesc('staff_count')
            ->take(5)
            ->get();

        // Revenue B2B vs B2C
        $revenueB2B = Payment::where('status', 'approved')
            ->whereIn('tenant_id', $b2bTenantIds)
            ->sum('amount');
        $revenueB2C = $revenueTotal - $revenueB2B;

        return view('super-admin.analytics.index', compact(
            'totalTenants',
            'activeTenants',
            'revenueThisMonth',
            'revenueLastMonth',
            'revenueTotal',
            'newTenantsPerMonth',
            'revenuePerMonth',
            'subscriptionDistribution',
            'topPlans',
            'revenueByPlan',
            'churnRate',
            'b2bTenantsPerMonth',
            'clinicalNotesPerMonth',
            'referralsPerMonth',
            'topFacilities',
            'revenueB2B',
            'revenueB2C',
        ));
    }
}
