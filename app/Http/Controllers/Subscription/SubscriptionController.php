<?php

namespace App\Http\Controllers\Subscription;

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
     * Free plans are activated immediately. Paid plans create a pending
     * subscription and redirect to the payment upload page.
     */
    public function subscribe(Plan $plan): RedirectResponse
    {
        $tenant = $this->tenantService->getCurrentTenant();

        if (! $tenant) {
            return redirect()->route('dashboard')
                ->with('error', __('status.no_organization'));
        }

        if (! $plan->is_active) {
            return redirect()->route('subscription.plans')
                ->with('error', __('status.plan_not_available'));
        }

        // Free plan: activate immediately, no payment needed
        if ($plan->price_monthly === 0) {
            $this->subscriptionService->activateFreePlan($tenant);

            return redirect()->route('subscription.current')
                ->with('success', __('status.free_plan_activated'));
        }

        // Paid plan: create pending subscription, then redirect to payment upload
        $subscription = $this->subscriptionService->createSubscription($tenant, $plan);

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
