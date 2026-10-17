<?php

namespace App\Services;

use App\Enums\TenantType;
use App\Exceptions\SubscriptionException;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\UploadedFile;

class SubscriptionService
{
    /**
     * Subscribe a tenant to a plan — SATU jalur logika untuk web & API.
     *
     * Guard:
     * - plan harus aktif;
     * - audience plan harus sesuai tipe tenant (family ↔ facility);
     * - plan gratis (is_free) diaktifkan langsung tanpa pembayaran;
     * - plan custom pricing (harga Rp0 tetapi bukan gratis) ditolak;
     * - plan berbayar membuat/mengembalikan subscription pending (dedup).
     *
     * @throws SubscriptionException
     */
    public function subscribeTenant(Tenant $tenant, Plan $plan): Subscription
    {
        if (! $plan->is_active) {
            throw new SubscriptionException(__('status.plan_not_available'));
        }

        $this->guardAudienceMatchesTenant($tenant, $plan);

        if ($plan->is_free) {
            return $this->activatePlan($tenant, $plan);
        }

        if ($plan->isCustomPricing()) {
            throw new SubscriptionException(__('subscription.errors.custom_pricing'));
        }

        // Batalkan pending subscription untuk plan lain (dedup & cleanup).
        $this->cancelPendingSubscriptions($tenant, $plan);

        // Klik berulang pada plan yang sama: gunakan pending yang sudah ada.
        $existingPending = $tenant->subscriptions()
            ->where('status', Subscription::STATUS_PENDING)
            ->where('plan_id', $plan->id)
            ->latest()
            ->first();

        return $existingPending ?? $this->createSubscription($tenant, $plan);
    }

    /**
     * Guard audience: tenant keluarga (B2C) hanya boleh plan family,
     * tenant fasilitas (B2B) hanya boleh plan facility.
     *
     * @throws SubscriptionException
     */
    private function guardAudienceMatchesTenant(Tenant $tenant, Plan $plan): void
    {
        $tenantType = $tenant->type instanceof TenantType ? $tenant->type : TenantType::Family;
        $requiredAudience = $tenantType->isB2B() ? Plan::AUDIENCE_FACILITY : Plan::AUDIENCE_FAMILY;

        if ($plan->audience !== $requiredAudience) {
            throw new SubscriptionException(__('subscription.errors.audience_mismatch'));
        }
    }

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
     * Aktifkan plan secara langsung tanpa pembayaran.
     *
     * Membatalkan semua subscription active & pending lama tenant,
     * lalu membuat subscription ACTIVE baru tanpa tanggal berakhir
     * (pola yang sama dengan activateFreePlan sebelumnya — free plan
     * berlaku selamanya, ends_at null).
     */
    public function activatePlan(Tenant $tenant, Plan $plan): Subscription
    {
        $this->cancelActiveSubscription($tenant);

        $tenant->subscriptions()
            ->where('status', Subscription::STATUS_PENDING)
            ->update([
                'status' => Subscription::STATUS_CANCELLED,
                'cancelled_at' => now(),
            ]);

        return Subscription::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'status' => Subscription::STATUS_ACTIVE,
            'starts_at' => now(),
            'ends_at' => null, // Free plan lasts forever — no expiry
        ]);
    }

    /**
     * Activate a free plan immediately (no payment needed).
     *
     * Dipertahankan untuk kompatibilitas (dipakai TenantService saat
     * pembuatan tenant) — kini mendelegasikan ke activatePlan() dengan
     * plan gratis yang ditemukan, bukan hardcode pembuatan subscription.
     */
    public function activateFreePlan(Tenant $tenant): Subscription
    {
        $freePlan = Plan::where('slug', 'free')->first()
            ?? Plan::where('is_free', true)->first();

        if (! $freePlan) {
            abort(500, 'Paket gratis tidak tersedia.');
        }

        return $this->activatePlan($tenant, $freePlan);
    }

    /**
     * Submit payment proof — SATU jalur logika untuk web & API.
     *
     * Validasi:
     * - subscription harus milik tenant (403);
     * - status subscription harus pending;
     * - amount minimal sama dengan harga bulanan plan.
     *
     * @param  array{bank_name: string, amount: int, notes?: string|null}  $data
     *
     * @throws SubscriptionException
     */
    public function submitPaymentProof(Tenant $tenant, Subscription $subscription, array $data, UploadedFile $proof): Payment
    {
        // Verify the subscription belongs to this tenant to prevent
        // unauthorized payment submissions (cross-tenant protection).
        abort_unless($subscription->tenant_id === $tenant->id, 403);

        if ($subscription->status !== Subscription::STATUS_PENDING) {
            throw new SubscriptionException(__('subscription.errors.payment_subscription_not_pending'));
        }

        $plan = $subscription->plan;

        if ($plan && $data['amount'] < $plan->price_monthly) {
            throw new SubscriptionException(__('subscription.errors.amount_below_price', [
                'amount' => 'Rp '.number_format($plan->price_monthly, 0, ',', '.'),
            ]));
        }

        $proofPath = $proof->store('payments/proofs', 'public');

        $bankConfig = config("saas.banks.{$data['bank_name']}", []);

        return Payment::create([
            'subscription_id' => $subscription->id,
            'tenant_id' => $tenant->id,
            'amount' => $data['amount'],
            'currency' => 'IDR',
            'payment_method' => Payment::METHOD_BANK_TRANSFER,
            'bank_name' => $data['bank_name'],
            'bank_account' => $bankConfig['account'] ?? null,
            'account_holder' => $bankConfig['holder'] ?? null,
            'proof_path' => $proofPath,
            'status' => Payment::STATUS_PENDING,
            'notes' => $data['notes'] ?? null,
            'paid_at' => now(),
        ]);
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

        // Cleanup: batalkan semua subscription pending sisa milik tenant
        // agar tidak menumpuk riwayat kotor setelah pembayaran disetujui.
        $this->cancelPendingSubscriptions($subscription->tenant, null, $subscription);
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
     * Cancel pending subscriptions milik tenant, dengan pengecualian
     * plan tertentu dan/atau subscription tertentu.
     */
    private function cancelPendingSubscriptions(Tenant $tenant, ?Plan $exceptPlan = null, ?Subscription $exceptSubscription = null): void
    {
        $query = $tenant->subscriptions()
            ->where('status', Subscription::STATUS_PENDING);

        if ($exceptPlan) {
            $query->where('plan_id', '!=', $exceptPlan->id);
        }

        if ($exceptSubscription) {
            $query->where('id', '!=', $exceptSubscription->id);
        }

        $query->update([
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
