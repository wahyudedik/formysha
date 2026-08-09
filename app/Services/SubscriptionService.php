<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;

class SubscriptionService
{
    /**
     * Create a new subscription for a tenant.
     */
    public function createSubscription(Tenant $tenant, Plan $plan): Subscription
    {
        return Subscription::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'status' => Subscription::STATUS_PENDING,
        ]);
    }

    /**
     * Activate a free plan immediately (no payment needed).
     */
    public function activateFreePlan(Tenant $tenant): Subscription
    {
        $freePlan = Plan::where('slug', 'free')->first();

        if (! $freePlan) {
            abort(500, 'Paket gratis tidak tersedia.');
        }

        // Cancel any existing active subscription
        $this->cancelActiveSubscription($tenant);

        $subscription = Subscription::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $freePlan->id,
            'status' => Subscription::STATUS_ACTIVE,
            'starts_at' => now(),
            'ends_at' => null, // Free plan lasts forever — no expiry
        ]);

        return $subscription;
    }

    /**
     * Approve a payment and activate the subscription.
     */
    public function approvePayment(Payment $payment, User $admin): void
    {
        $payment->update([
            'status' => Payment::STATUS_APPROVED,
            'verified_by' => $admin->id,
            'verified_at' => now(),
        ]);

        $subscription = $payment->subscription;

        if (! $subscription) {
            abort(404, 'Langganan tidak ditemukan.');
        }

        // Cancel any other active subscription for this tenant
        $this->cancelActiveSubscription($subscription->tenant);

        // Calculate billing period
        $plan = $subscription->plan;
        $startsAt = now();
        $endsAt = $plan->price_yearly && $payment->amount >= $plan->price_yearly
            ? now()->addYear()
            : now()->addMonth();

        $subscription->update([
            'status' => Subscription::STATUS_ACTIVE,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
        ]);
    }

    /**
     * Reject a payment.
     */
    public function rejectPayment(Payment $payment, User $admin, string $reason): void
    {
        $payment->update([
            'status' => Payment::STATUS_REJECTED,
            'verified_by' => $admin->id,
            'verified_at' => now(),
            'notes' => $reason,
        ]);

        // Set subscription back to inactive if no other pending payments
        $subscription = $payment->subscription;

        if ($subscription) {
            $hasOtherPending = $subscription->payments()
                ->where('status', Payment::STATUS_PENDING)
                ->exists();

            if (! $hasOtherPending) {
                $subscription->update([
                    'status' => Subscription::STATUS_INACTIVE,
                ]);
            }
        }
    }

    /**
     * Cancel a subscription.
     */
    public function cancelSubscription(Subscription $subscription): void
    {
        $subscription->update([
            'status' => Subscription::STATUS_CANCELLED,
            'cancelled_at' => now(),
        ]);
    }

    /**
     * Cancel any active subscription for a tenant.
     */
    public function cancelActiveSubscription(Tenant $tenant): void
    {
        $tenant->subscriptions()
            ->where('status', Subscription::STATUS_ACTIVE)
            ->update([
                'status' => Subscription::STATUS_CANCELLED,
                'cancelled_at' => now(),
            ]);
    }

    /**
     * Get the active subscription for a tenant.
     */
    public function getActiveSubscription(Tenant $tenant): ?Subscription
    {
        return $tenant->activeSubscription;
    }

    /**
     * Check if a tenant has an active subscription.
     */
    public function isSubscriptionActive(Tenant $tenant): bool
    {
        return $tenant->hasActiveSubscription();
    }

    /**
     * Check and expire overdue subscriptions.
     */
    public function checkExpiredSubscriptions(): int
    {
        return Subscription::where('status', Subscription::STATUS_ACTIVE)
            ->where('ends_at', '<', now())
            ->update([
                'status' => Subscription::STATUS_PAST_DUE,
            ]);
    }
}
