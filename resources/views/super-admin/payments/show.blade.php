<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                💳 {{ __('Detail Pembayaran') }}
            </h2>
            @if ($payment->status === 'pending')
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        class="inline-flex items-center px-4 py-2 bg-mintGreen-500 text-white text-sm font-semibold rounded-xl hover:bg-mintGreen-600 transition"
                        x-data
                        x-on:click="$dispatch('open-modal', 'approve-payment')"
                    >
                        ✅ Setujui
                    </button>
                    <button
                        type="button"
                        class="inline-flex items-center px-4 py-2 bg-red-500 text-white text-sm font-semibold rounded-xl hover:bg-red-600 transition"
                        x-data
                        x-on:click="$dispatch('open-modal', 'reject-payment')"
                    >
                        ❌ Tolak
                    </button>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-6">
            @include('super-admin.partials.sidebar')

            <div class="flex-1 min-w-0">
                <x-breadcrumb :items="[
                    ['label' => 'Dashboard', 'url' => route('super-admin.dashboard')],
                    ['label' => 'Pembayaran', 'url' => route('super-admin.payments.index')],
                    ['label' => 'Detail'],
                ]" />

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Payment Info --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft overflow-hidden">
                        <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="font-semibold text-gray-800 dark:text-gray-100">📋 {{ __('Informasi Pembayaran') }}</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Status</span>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium
                                    {{ match($payment->status) {
                                        'pending' => 'bg-warmYellow-100 text-warmYellow-600 dark:bg-warmYellow-950/30 dark:text-warmYellow-400',
                                        'approved' => 'bg-mintGreen-100 text-mintGreen-600 dark:bg-mintGreen-950/30 dark:text-mintGreen-400',
                                        'rejected' => 'bg-red-100 text-red-600 dark:bg-red-950/30 dark:text-red-400',
                                        default => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300',
                                    } }}">
                                    {{ match($payment->status) {
                                        'pending' => '⏳ Menunggu Verifikasi',
                                        'approved' => '✅ Disetujui',
                                        'rejected' => '❌ Ditolak',
                                        default => ucfirst($payment->status),
                                    } }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Jumlah</span>
                                <span class="text-lg font-bold text-gray-800 dark:text-gray-100">{{ $payment->getAmountFormatted() }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Tenant</span>
                                <span class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $payment->tenant->name ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Paket</span>
                                <span class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $payment->subscription->plan->name ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Bank</span>
                                <span class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $payment->bank_name ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500 dark:text-gray-400">No. Rekening</span>
                                <span class="text-sm font-medium text-gray-800 dark:text-gray-100 font-mono">{{ $payment->bank_account ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Atas Nama</span>
                                <span class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $payment->account_holder ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Tanggal Bayar</span>
                                <span class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $payment->paid_at?->locale('id')->isoFormat('D MMMM YYYY, HH:mm') ?? '-' }}</span>
                            </div>
                            @if ($payment->notes)
                                <div class="pt-3 border-t border-gray-100 dark:border-gray-700">
                                    <span class="text-sm text-gray-500 dark:text-gray-400 block mb-1">Catatan:</span>
                                    <p class="text-sm text-gray-700 dark:text-gray-200 bg-gray-50 dark:bg-gray-700/50 rounded-xl p-3">{{ $payment->notes }}</p>
                                </div>
                            @endif
                            @if ($payment->verifier)
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Diverifikasi oleh</span>
                                    <span class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $payment->verifier->name }}</span>
                                </div>
                            @endif
                            @if ($payment->verified_at)
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Waktu Verifikasi</span>
                                    <span class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $payment->verified_at->locale('id')->isoFormat('D MMMM YYYY, HH:mm') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Proof Image --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft overflow-hidden">
                        <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="font-semibold text-gray-800 dark:text-gray-100">🖼️ {{ __('Bukti Transfer') }}</h3>
                        </div>
                        <div class="p-6">
                            @if ($payment->proof_path)
                                <div class="rounded-xl overflow-hidden border border-gray-100 dark:border-gray-700">
                                    <img
                                        src="{{ asset('storage/' . $payment->proof_path) }}"
                                        alt="Bukti Transfer"
                                        class="w-full h-auto object-contain max-h-96"
                                    >
                                </div>
                                <div class="mt-3 text-center">
                                    <a href="{{ asset('storage/' . $payment->proof_path) }}" target="_blank" class="text-sm text-skyBlue-600 hover:text-skyBlue-700 dark:text-skyBlue-400 dark:hover:text-skyBlue-300 transition">
                                        🔗 Buka dalam tab baru
                                    </a>
                                </div>
                            @else
                                <div class="text-center py-12">
                                    <div class="text-4xl mb-3">📷</div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada bukti transfer.</p>
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
        <div class="p-6">
            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-2">✅ Setujui Pembayaran</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Konfirmasi bahwa pembayaran ini telah diterima.</p>

            <form method="POST" action="{{ route('super-admin.payments.approve', $payment) }}">
                @csrf

                <div class="mb-4">
                    <x-input-label for="approve-notes" :value="__('Catatan (opsional)')" />
                    <textarea id="approve-notes" name="notes" rows="3" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-xl focus:border-mintGreen-300 focus:ring-mintGreen-200 text-sm" placeholder="Tambahkan catatan jika diperlukan...">{{ old('notes') }}</textarea>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" x-on:click="$dispatch('close-modal', 'approve-payment')" class="px-4 py-2 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-mintGreen-500 text-white text-sm font-semibold hover:bg-mintGreen-600 transition">
                        Ya, Setujui
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    {{-- Reject Modal --}}
    <x-modal name="reject-payment" :show="false" maxWidth="md">
        <div class="p-6">
            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-2">❌ Tolak Pembayaran</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Berikan alasan penolakan pembayaran ini.</p>

            <form method="POST" action="{{ route('super-admin.payments.reject', $payment) }}">
                @csrf

                <div class="mb-4">
                    <x-input-label for="reject-notes" :value="__('Alasan Penolakan')" />
                    <textarea id="reject-notes" name="notes" rows="3" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-xl focus:border-red-300 focus:ring-red-200 text-sm" placeholder="Jelaskan alasan penolakan..." required>{{ old('notes') }}</textarea>
                    <x-input-error :messages="$errors->get('notes')" class="mt-1" />
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" x-on:click="$dispatch('close-modal', 'reject-payment')" class="px-4 py-2 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-red-500 text-white text-sm font-semibold hover:bg-red-600 transition">
                        Ya, Tolak
                    </button>
                </div>
            </form>
        </div>
    </x-modal>
</x-app-layout>
