<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\Payment;
use App\Services\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(
        private SubscriptionService $subscriptionService,
    ) {}

    /**
     * Display a listing of payments.
     */
    public function index(): View
    {
        $payments = Payment::with(['subscription.plan', 'tenant', 'verifier'])
            ->latest()
            ->paginate(20);

        return view('super-admin.payments.index', compact('payments'));
    }

    /**
     * Display the specified payment.
     */
    public function show(Payment $payment): View
    {
        $payment->load(['subscription.plan', 'tenant', 'verifier']);

        return view('super-admin.payments.show', compact('payment'));
    }

    /**
     * Approve the specified payment.
     */
    public function approve(Request $request, Payment $payment): RedirectResponse
    {
        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $this->subscriptionService->approvePayment($payment, $request->user());

        if (! empty($validated['notes'])) {
            $payment->update(['notes' => $validated['notes']]);
        }

        return redirect()->route('super-admin.payments.index')
            ->with('status', __('status.payment_approved'));
    }

    /**
     * Reject the specified payment.
     */
    public function reject(Request $request, Payment $payment): RedirectResponse
    {
        $validated = $request->validate([
            'notes' => ['required', 'string', 'max:500'],
        ]);

        $this->subscriptionService->rejectPayment($payment, $request->user(), $validated['notes']);

        return redirect()->route('super-admin.payments.index')
            ->with('status', __('status.payment_rejected'));
    }
}
