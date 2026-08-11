<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                💳 {{ __('payments.detail') }}
            </h2>
            @if ($payment->status === 'pending')
                <div class="flex flex-row flex-wrap gap-2">
                    <button
                        type="button"
                        class="inline-flex items-center min-h-[44px] px-4 py-2 bg-mintGreen-500 text-white text-sm font-semibold rounded-xl hover:bg-mintGreen-600 transition"
                        x-data
                        x-on:click="$dispatch('open-modal', 'approve-payment')"
                    >
                        ✅ {{ __('actions.approve') }}
                    </button>
                    <button
                        type="button"
                        class="inline-flex items-center min-h-[44px] px-4 py-2 bg-red-500 text-white text-sm font-semibold rounded-xl hover:bg-red-600 transition"
                        x-data
                        x-on:click="$dispatch('open-modal', 'reject-payment')"
                    >
                        ❌ {{ __('actions.reject') }}
                    </button>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-6">
            @include('super-admin.partials.sidebar')

            <div class="flex-1 min-w-0">
                <x-breadcrumb :items="[
                    ['label' => __('navigation.dashboard'), 'url' => route('super-admin.dashboard')],
                    ['label' => __('navigation.payments'), 'url' => route('super-admin.payments.index')],
                    ['label' => __('common.detail')],
                ]" />

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Payment Info --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft overflow-hidden">
                        <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="font-semibold text-gray-800 dark:text-gray-100">📋 {{ __('payments.info') }}</h3>
                        </div>
                        <div class="p-4 sm:p-6 space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('payments.status') }}</span>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium
                                    {{ match($payment->status) {
                                        'pending' => 'bg-warmYellow-100 text-warmYellow-600 dark:bg-warmYellow-950/30 dark:text-warmYellow-400',
                                        'approved' => 'bg-mintGreen-100 text-mintGreen-600 dark:bg-mintGreen-950/30 dark:text-mintGreen-400',
                                        'rejected' => 'bg-red-100 text-red-600 dark:bg-red-950/30 dark:text-red-400',
                                        default => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300',
                                    } }}">
                                    {{ match($payment->status) {
                                        'pending' => '⏳ ' . __('payments.waiting_verification'),
                                        'approved' => '✅ ' . __('payments.approved'),
                                        'rejected' => '❌ ' . __('payments.rejected'),
                                        default => ucfirst($payment->status),
                                    } }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('payments.amount') }}</span>
                                <span class="text-lg font-bold text-gray-800 dark:text-gray-100">{{ $payment->getAmountFormatted() }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('payments.tenant') }}</span>
                                <span class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $payment->tenant->name ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('payments.plan') }}</span>
                                <span class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $payment->subscription->plan->name ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('payments.bank') }}</span>
                                <span class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $payment->bank_name ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('payments.bank_account') }}</span>
                                <span class="text-sm font-medium text-gray-800 dark:text-gray-100 font-mono">{{ $payment->bank_account ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('payments.account_holder') }}</span>
                                <span class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $payment->account_holder ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('payments.paid_date') }}</span>
                                <span class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $payment->paid_at?->locale('id')->isoFormat('D MMMM YYYY, HH:mm') ?? '-' }}</span>
                            </div>
                            @if ($payment->notes)
                                <div class="pt-3 border-t border-gray-100 dark:border-gray-700">
                                    <span class="text-sm text-gray-500 dark:text-gray-400 block mb-1">{{ __('payments.notes_label') }}</span>
                                    <p class="text-sm text-gray-700 dark:text-gray-200 bg-gray-50 dark:bg-gray-700/50 rounded-xl p-3">{{ $payment->notes }}</p>
                                </div>
                            @endif
                            @if ($payment->verifier)
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('payments.verified_by') }}</span>
                                    <span class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $payment->verifier->name }}</span>
                                </div>
                            @endif
                            @if ($payment->verified_at)
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('payments.verification_time') }}</span>
                                    <span class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $payment->verified_at->locale('id')->isoFormat('D MMMM YYYY, HH:mm') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Proof Image --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft overflow-hidden">
                        <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="font-semibold text-gray-800 dark:text-gray-100">🖼️ {{ __('payments.proof_transfer') }}</h3>
                        </div>
                        <div class="p-4 sm:p-6">
                            @if ($payment->proof_path)
                                <div class="rounded-xl overflow-hidden border border-gray-100 dark:border-gray-700">
                                    <img
                                        src="{{ asset('storage/' . $payment->proof_path) }}"
                                        alt="{{ __('payments.proof_transfer') }}"
                                        class="w-full h-auto object-contain max-h-96"
                                    >
                                </div>
                                <div class="mt-3 text-center">
                                    <a href="{{ asset('storage/' . $payment->proof_path) }}" target="_blank" class="text-sm text-skyBlue-600 hover:text-skyBlue-700 dark:text-skyBlue-400 dark:hover:text-skyBlue-300 transition">
                                        🔗 {{ __('payments.open_new_tab') }}
                                    </a>
                                </div>
                            @else
                                <div class="text-center py-12">
                                    <div class="text-4xl mb-3">📷</div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('empty_states.no_payment_proof') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Approve Modal --}}
    <x-modal name="approve-payment" :show="false" maxWidth="md">
        <div class="p-4 sm:p-6">
            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-2">✅ {{ __('empty_states.approve_payment_title') }}</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ __('empty_states.approve_payment_desc') }}</p>

            <form method="POST" action="{{ route('super-admin.payments.approve', $payment) }}" x-data="{ loading: false }" @submit="loading = true">
                @csrf

                <div class="mb-4">
                    <x-input-label for="approve-notes" :value="__('payments.notes_optional')" />
                    <textarea id="approve-notes" name="notes" rows="3" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-xl focus:border-mintGreen-300 focus:ring-mintGreen-200 text-sm" placeholder="Tambahkan catatan jika diperlukan...">{{ old('notes') }}</textarea>
                </div>

                <div class="flex flex-col sm:flex-row justify-end gap-3">
                    <button type="button" x-on:click="$dispatch('close-modal', 'approve-payment')" class="min-h-[44px] px-4 py-2 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                        {{ __('actions.cancel') }}
                    </button>
                    <button type="submit" x-bind:disabled="loading" class="min-h-[44px] px-4 py-2 rounded-xl bg-mintGreen-500 text-white text-sm font-semibold hover:bg-mintGreen-600 transition disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center justify-center gap-2">
                        <svg x-show="loading" class="w-4 h-4 shrink-0 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span x-text="loading ? '{{ __('payments.approving') }}' : '{{ __('payments.confirm_approve') }}'"></span>
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    {{-- Reject Modal --}}
    <x-modal name="reject-payment" :show="false" maxWidth="md">
        <div class="p-4 sm:p-6">
            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-2">❌ {{ __('payments.reject_payment') }}</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ __('payments.reject_reason_desc') }}</p>

            <form method="POST" action="{{ route('super-admin.payments.reject', $payment) }}" x-data="{ loading: false }" @submit="loading = true">
                @csrf

                <div class="mb-4">
                    <x-input-label for="reject-notes" :value="__('payments.reject_reason')" />
                    <textarea id="reject-notes" name="notes" rows="3" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-xl focus:border-red-300 focus:ring-red-200 text-sm" placeholder="Jelaskan alasan penolakan..." required>{{ old('notes') }}</textarea>
                    <x-input-error :messages="$errors->get('notes')" class="mt-1" />
                </div>

                <div class="flex flex-col sm:flex-row justify-end gap-3">
                    <button type="button" x-on:click="$dispatch('close-modal', 'reject-payment')" class="min-h-[44px] px-4 py-2 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                        {{ __('actions.cancel') }}
                    </button>
                    <button type="submit" x-bind:disabled="loading" class="min-h-[44px] px-4 py-2 rounded-xl bg-red-500 text-white text-sm font-semibold hover:bg-red-600 transition disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center justify-center gap-2">
                        <svg x-show="loading" class="w-4 h-4 shrink-0 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span x-text="loading ? '{{ __('payments.rejecting') }}' : '{{ __('payments.confirm_reject') }}'"></span>
                    </button>
                </div>
            </form>
        </div>
    </x-modal>
</x-app-layout>
