<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\SubscriptionException;
use App\Models\Plan;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use App\Services\TenantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionApiController extends ApiController
{
    public function __construct(
        private TenantService $tenantService,
        private SubscriptionService $subscriptionService,
    ) {}

    /**
     * Get the current user's active subscription.
     */
    public function current(Request $request): JsonResponse
    {
        $tenant = $request->user()->tenant;

        if (! $tenant) {
            return $this->errorResponse(__('status.no_organization'), 404);
        }

        $subscription = $tenant->activeSubscription()->with('plan')->first();

        if (! $subscription) {
            return $this->errorResponse(__('subscription.no_subscription'), 404);
        }

        return $this->successResponse($subscription, __('subscription.current_plan'));
    }

    /**
     * Get subscription history for the current user.
     */
    public function history(Request $request): JsonResponse
    {
        $tenant = $request->user()->tenant;

        if (! $tenant) {
            return $this->errorResponse(__('status.no_organization'), 404);
        }

        $subscriptions = Subscription::where('tenant_id', $tenant->id)
            ->with('plan')
            ->latest()
            ->paginate($request->input('per_page', 15));

        return $this->paginatedResponse($subscriptions, __('subscription.history'));
    }

    /**
     * Subscribe to a plan.
     *
     * Logika subscribe disatukan di SubscriptionService::subscribeTenant():
     * free plan aktif langsung, plan berbayar membuat pending subscription,
     * plan custom pricing ditolak dengan pesan jelas.
     */
    public function subscribe(Request $request, Plan $plan): JsonResponse
    {
        $tenant = $request->user()->tenant;

        if (! $tenant) {
            return $this->errorResponse(__('status.no_organization'), 404);
        }

        try {
            $subscription = $this->subscriptionService->subscribeTenant($tenant, $plan);
        } catch (SubscriptionException $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }

        // Free plan: aktif langsung tanpa pembayaran
        if ($subscription->status === Subscription::STATUS_ACTIVE) {
            return $this->successResponse(
                $subscription->load('plan'),
                __('status.free_plan_activated'),
            );
        }

        // Paid plan: pending → arahkan melakukan pembayaran
        return $this->successResponse(
            $subscription->load('plan'),
            __('status.subscription_created'),
            201
        );
    }

    /**
     * Upload payment proof.
     *
     * Validasi kepemilikan (subscription milik tenant), status pending,
     * dan amount minimal harga plan ditangani oleh
     * SubscriptionService::submitPaymentProof().
     */
    public function uploadPayment(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'subscription_id' => ['required', 'exists:subscriptions,id'],
            'bank_name' => ['required', 'string', 'max:50'],
            'amount' => ['required', 'integer', 'min:1'],
            'proof' => ['required', 'file', 'image', 'max:5120'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $tenant = $request->user()->tenant;

        if (! $tenant) {
            return $this->errorResponse(__('status.no_organization'), 404);
        }

        $subscription = Subscription::findOrFail($validated['subscription_id']);

        try {
            $payment = $this->subscriptionService->submitPaymentProof(
                $tenant,
                $subscription,
                $validated,
                $request->file('proof'),
            );
        } catch (SubscriptionException $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }

        return $this->successResponse($payment, __('status.payment_proof_sent'), 201);
    }
}
