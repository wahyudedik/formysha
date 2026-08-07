<?php

namespace App\Http\Controllers\Subscription;

use App\Models\Payment;
use App\Models\Subscription;
use App\Services\TenantService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(
        private TenantService $tenantService,
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
                ->with('error', 'Anda belum memiliki organisasi.');
        }

        $proofPath = $request->file('proof')->store('payments/proofs', 'public');

        $bankConfig = config("saas.banks.{$validated['bank_name']}", []);

        Payment::create([
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

        return redirect()->route('subscription.current')
            ->with('success', 'Bukti pembayaran berhasil dikirim. Menunggu verifikasi admin.');
    }
}
