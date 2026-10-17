<?php

namespace App\Http\Controllers\Subscription;

use App\Exceptions\SubscriptionException;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use App\Services\TenantService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(
        private TenantService $tenantService,
        private SubscriptionService $subscriptionService,
    ) {}

    /**
     * Show payment upload form.
     */
    public function upload(Subscription $subscription): View
    {
        $banks = config('saas.banks', []);

        return view('subscription.payment-upload', compact('subscription', 'banks'));
    }

    /**
     * Store payment proof.
     *
     * Validasi kepemilikan (subscription milik tenant), status pending,
     * dan amount minimal harga plan ditangani oleh
     * SubscriptionService::submitPaymentProof().
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subscription_id' => ['required', 'exists:subscriptions,id'],
            'bank_name' => ['required', 'string', 'max:50'],
            'amount' => ['required', 'integer', 'min:1'],
            'proof' => ['required', 'file', 'image', 'max:5120'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $tenant = $this->tenantService->getCurrentTenant();

        if (! $tenant) {
            return redirect()->route('dashboard')
                ->with('error', __('status.no_organization'));
        }

        $subscription = Subscription::findOrFail($validated['subscription_id']);

        try {
            $this->subscriptionService->submitPaymentProof(
                $tenant,
                $subscription,
                $validated,
                $request->file('proof'),
            );
        } catch (SubscriptionException $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }

        return redirect()->route('subscription.current')
            ->with('status', __('status.payment_proof_sent'));
    }
}
