<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Api\ApiController;
use App\Models\Payment;
use App\Services\SubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends ApiController
{
    public function __construct(
        private SubscriptionService $subscriptionService,
    ) {}

    /**
     * List all payments with optional status filter.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Payment::with(['subscription.plan', 'tenant', 'verifier']);

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $payments = $query->latest()->paginate($request->input('per_page', 15));

        return $this->paginatedResponse($payments, 'Daftar pembayaran berhasil diambil');
    }

    /**
     * Show a specific payment.
     */
    public function show(Payment $payment): JsonResponse
    {
        $payment->load(['subscription.plan', 'tenant', 'verifier']);

        return $this->successResponse($payment, 'Detail pembayaran berhasil diambil');
    }

    /**
     * Approve a payment and activate the subscription.
     */
    public function approve(Request $request, Payment $payment): JsonResponse
    {
        if ($payment->status !== Payment::STATUS_PENDING) {
            return $this->errorResponse('Hanya pembayaran status pending yang dapat disetujui', 422);
        }

        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $this->subscriptionService->approvePayment($payment, $request->user());

        if (! empty($validated['notes'])) {
            $payment->update(['notes' => $validated['notes']]);
        }

        return $this->successResponse($payment->fresh()->load(['subscription.plan', 'tenant']), 'Pembayaran berhasil disetujui');
    }

    /**
     * Reject a payment with a reason.
     */
    public function reject(Request $request, Payment $payment): JsonResponse
    {
        if ($payment->status !== Payment::STATUS_PENDING) {
            return $this->errorResponse('Hanya pembayaran status pending yang dapat ditolak', 422);
        }

        $validated = $request->validate([
            'notes' => ['required', 'string', 'max:500'],
        ]);

        $this->subscriptionService->rejectPayment($payment, $request->user(), $validated['notes']);

        return $this->successResponse($payment->fresh()->load(['subscription.plan', 'tenant']), 'Pembayaran berhasil ditolak');
    }
}
