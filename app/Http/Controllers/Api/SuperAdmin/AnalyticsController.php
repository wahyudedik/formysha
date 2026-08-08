<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Api\ApiController;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnalyticsController extends ApiController
{
    /**
     * Return analytics data for Super Admin.
     */
    public function index(Request $request): JsonResponse
    {
        // Total tenants
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

        // Churn rate
        $cancelledLast30Days = Subscription::where('status', 'cancelled')
            ->where('cancelled_at', '>=', now()->subDays(30))
            ->count();
        $totalActive = Subscription::where('status', 'active')->count();
        $churnRate = $totalActive > 0
            ? round(($cancelledLast30Days / $totalActive) * 100, 1)
            : 0;

        $analytics = [
            'total_tenants' => $totalTenants,
            'active_tenants' => $activeTenants,
            'revenue_this_month' => $revenueThisMonth,
            'revenue_last_month' => $revenueLastMonth,
            'revenue_total' => $revenueTotal,
            'new_tenants_per_month' => $newTenantsPerMonth,
            'revenue_per_month' => $revenuePerMonth,
            'subscription_distribution' => $subscriptionDistribution,
            'top_plans' => $topPlans,
            'revenue_by_plan' => $revenueByPlan,
            'churn_rate' => $churnRate,
        ];

        return $this->successResponse($analytics, 'Data analytics berhasil diambil');
    }
}
