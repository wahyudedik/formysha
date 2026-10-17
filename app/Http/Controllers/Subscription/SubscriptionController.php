<?php

namespace App\Http\Controllers\Subscription;

use App\Exceptions\SubscriptionException;
use App\Models\Plan;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use App\Services\TenantService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function __construct(
        private TenantService $tenantService,
        private SubscriptionService $subscriptionService,
    ) {}

    /**
     * Display available plans.
     */
    public function plans(): View
    {
        $plans = Plan::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('subscription.plans', compact('plans'));
    }

    /**
     * Subscribe to a plan.
     *
     * Logika subscribe disatukan di SubscriptionService::subscribeTenant():
     * free plan aktif langsung, plan berbayar membuat pending subscription
     * lalu redirect ke halaman upload pembayaran, plan custom pricing ditolak.
     */
    public function subscribe(Plan $plan): RedirectResponse
    {
        $tenant = $this->tenantService->getCurrentTenant();

        if (! $tenant) {
            return redirect()->route('dashboard')
                ->with('error', __('status.no_organization'));
        }

        try {
            $subscription = $this->subscriptionService->subscribeTenant($tenant, $plan);
        } catch (SubscriptionException $e) {
            return redirect()->route('subscription.plans')
                ->with('error', $e->getMessage());
        }

        // Free plan: sudah aktif langsung tanpa pembayaran
        if ($subscription->status === Subscription::STATUS_ACTIVE) {
            return redirect()->route('subscription.current')
                ->with('success', __('status.free_plan_activated'));
        }

        // Paid plan: pending → redirect ke payment upload
        return redirect()->route('subscription.payment.upload', $subscription)
            ->with('success', __('status.subscription_created'));
    }

    /**
     * Display current subscription.
     */
    public function current(): View
    {
        $tenant = $this->tenantService->getCurrentTenant();
        $subscription = $tenant?->activeSubscription()->with('plan')->first();

        return view('subscription.current', compact('tenant', 'subscription'));
    }

    /**
     * Display subscription history.
     */
    public function history(): View
    {
        $tenant = $this->tenantService->getCurrentTenant();

        $subscriptions = Subscription::where('tenant_id', $tenant?->id)
            ->with('plan')
            ->latest()
            ->paginate(20);

        return view('subscription.history', compact('subscriptions'));
    }
}
