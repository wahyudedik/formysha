<?php

namespace App\Http\Controllers\Api;

use App\Models\Payment;
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
            return $this->errorResponse('Anda belum memiliki organisasi', 404);
        }

        $subscription = $tenant->activeSubscription()->with('plan')->first();

        if (! $subscription) {
            return $this->errorResponse('Tidak ada langganan aktif', 404);
        }

        return $this->successResponse($subscription, 'Langganan aktif berhasil diambil');
    }

    /**
     * Get subscription history for the current user.
     */
    public function history(Request $request): JsonResponse
    {
        $tenant = $request->user()->tenant;

        if (! $tenant) {
            return $this->errorResponse('Anda belum memiliki organisasi', 404);
        }

        $subscriptions = Subscription::where('tenant_id', $tenant->id)
            ->with('plan')
            ->latest()
            ->paginate($request->input('per_page', 15));

        return $this->paginatedResponse($subscriptions, 'Riwayat langganan berhasil diambil');
    }

    /**
     * Subscribe to a plan.
     */
    public function subscribe(Request $request, Plan $plan): JsonResponse
    {
        $tenant = $request->user()->tenant;

        if (! $tenant) {
            return $this->errorResponse('Anda belum memiliki organisasi', 404);
        }

        if (! $plan->is_active) {
            return $this->errorResponse('Paket ini tidak tersedia', 422);
        }

        $subscription = $this->subscriptionService->createSubscription($tenant, $plan);

        return $this->successResponse(
            $subscription->load('plan'),
            'Langganan berhasil dibuat. Silakan lakukan pembayaran.',
            201
        );
    }

    /**
     * Upload payment proof.
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
            return $this->errorResponse('Anda belum memiliki organisasi', 404);
        }

        $proofPath = $request->file('proof')->store('payments/proofs', 'public');

        $bankConfig = config("saas.banks.{$validated['bank_name']}", []);

        $payment = Payment::create([
            'subscription_id' => $validated['subscription_id'],
            'tenant_id' => $tenant->id,
            'amount' => $validated['amount'],
            'currency' => 'IDR',
            'payment_method' => Payment::METHOD_BANK_TRANSFER,
            'bank_name' => $validated['bank_name'],
            'bank_account' => $bankConfig['account'] ?? null,
            'account_holder' => $bankConfig['holder'] ?? null,
            'proof_path' => $proofPath,
            'status' => Payment::STATUS_PENDING,
            'notes' => $validated['notes'] ?? null,
            'paid_at' => now(),
        ]);

        return $this->successResponse($payment, 'Bukti pembayaran berhasil dikirim. Menunggu verifikasi admin.', 201);
    }
}
