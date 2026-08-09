<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            💎 {{ __('Langganan Saya') }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-breadcrumb :items="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Langganan'],
        ]" />

        @if ($subscription)
            {{-- Current Plan Card --}}
            <div class="bg-gradient-to-br from-softPink-50 via-white to-lavender-50 dark:from-softPink-950/30 dark:via-gray-800 dark:to-lavender-950/30 rounded-3xl border border-softPink-100 dark:border-softPink-900/50 overflow-hidden mb-6">
                <div class="p-6 sm:p-8">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $subscription->plan->name ?? '-' }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Paket langganan aktif Anda</p>
                        </div>
                        <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-sm font-medium self-start
                            {{ match($subscription->status) {
                                'active' => 'bg-mintGreen-100 text-mintGreen-600 dark:bg-mintGreen-950/30 dark:text-mintGreen-400',
                                'pending' => 'bg-warmYellow-100 text-warmYellow-600 dark:bg-warmYellow-950/30 dark:text-warmYellow-400',
                                'inactive' => 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400',
                                'cancelled' => 'bg-red-100 text-red-600 dark:bg-red-950/30 dark:text-red-400',
                                'past_due' => 'bg-orange-100 text-orange-600 dark:bg-orange-950/30 dark:text-orange-400',
                                default => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
                            } }}">
                            {{ match($subscription->status) {
                                'active' => '✅ Aktif',
                                'pending' => '⏳ Menunggu Pembayaran',
                                'inactive' => '⏸️ Tidak Aktif',
                                'cancelled' => '❌ Dibatalkan',
                                'past_due' => '⚠️ Terlambat Bayar',
                                default => ucfirst($subscription->status),
                            } }}
                        </span>
                    </div>

                    {{-- Plan Details --}}
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div class="bg-white/80 dark:bg-gray-700/50 rounded-xl p-4">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Harga</p>
                            <p class="text-lg font-bold text-gray-800 dark:text-gray-100">{{ $subscription->plan->getPriceMonthlyFormatted() }}</p>
                            <p class="text-[10px] text-gray-400 dark:text-gray-500">per bulan</p>
                        </div>
                        <div class="bg-white/80 dark:bg-gray-700/50 rounded-xl p-4">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Mulai</p>
                            <p class="text-sm font-bold text-gray-800 dark:text-gray-100">{{ $subscription->starts_at?->locale('id')->isoFormat('D MMM YYYY') ?? '-' }}</p>
                        </div>
                        <div class="bg-white/80 dark:bg-gray-700/50 rounded-xl p-4">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Berakhir</p>
                            <p class="text-sm font-bold text-gray-800 dark:text-gray-100">{{ $subscription->ends_at?->locale('id')->isoFormat('D MMM YYYY') ?? '-' }}</p>
                        </div>
                        <div class="bg-white/80 dark:bg-gray-700/50 rounded-xl p-4">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Sisa Hari</p>
                            <p class="text-lg font-bold text-gray-800 dark:text-gray-100">{{ $subscription->daysRemaining() }}</p>
                        </div>
                    </div>

                    {{-- Progress Bar --}}
                    @if ($subscription->starts_at && $subscription->ends_at)
                        @php
                            $totalDays = $subscription->starts_at->diffInDays($subscription->ends_at);
                            $elapsed = $subscription->starts_at->diffInDays(now());
                            $percent = $totalDays > 0 ? min(100, round(($elapsed / $totalDays) * 100)) : 0;
                        @endphp
                        <div class="mt-6">
                            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mb-1">
                                <span>Penggunaan</span>
                                <span>{{ $percent }}%</span>
                            </div>
                            <div class="w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-softPink-400 to-lavender-400 rounded-full transition-all" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Usage Summary --}}
            @if ($subscription->plan)
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft overflow-hidden mb-6">
                    <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-100">📊 {{ __('Ringkasan Penggunaan') }}</h3>
                    </div>
                    <div class="p-4 sm:p-6 grid grid-cols-2 sm:grid-cols-3 gap-4">
                        <div class="text-center p-3 rounded-xl bg-skyBlue-50 dark:bg-skyBlue-950/30">
                            <p class="text-xl font-bold text-skyBlue-600 dark:text-skyBlue-400">{{ $tenant?->children_count ?? 0 }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Anak</p>
                            <p class="text-[10px] text-gray-400 dark:text-gray-500">/ {{ $subscription->plan->max_children === -1 ? '∞' : $subscription->plan->max_children }}</p>
                        </div>
                        <div class="text-center p-3 rounded-xl bg-softPink-50 dark:bg-softPink-950/30">
                            <p class="text-xl font-bold text-softPink-600 dark:text-softPink-400">{{ $tenant?->payments()->where('status', 'approved')->count() ?? 0 }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Pembayaran</p>
                        </div>
                        <div class="text-center p-3 rounded-xl bg-lavender-50 dark:bg-lavender-950/30">
                            <p class="text-xl font-bold text-lavender-600 dark:text-lavender-400">{{ $subscription->plan->getStorageFormatted() }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Storage</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Actions --}}
            <div class="flex flex-col sm:flex-row flex-wrap gap-3">
                @if ($subscription->status === 'pending')
                    <a href="{{ route('subscription.payment.upload', $subscription) }}" class="btn-primary text-sm min-h-[44px] inline-flex items-center">
                        💳 {{ __('Upload Bukti Pembayaran') }}
                    </a>
                @endif
                <a href="{{ route('subscription.plans') }}" class="btn-accent text-sm min-h-[44px] inline-flex items-center">
                    🔄 {{ __('Upgrade / Ganti Paket') }}
                </a>
                <a href="{{ route('subscription.history') }}" class="btn-secondary text-sm min-h-[44px] inline-flex items-center">
                    📜 {{ __('Riwayat Langganan') }}
                </a>
            </div>
        @else
            {{-- No Active Subscription --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft overflow-hidden">
                <div class="p-6 sm:p-12 text-center">
                    <div class="text-6xl mb-4">💎</div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-2">Belum Ada Langganan</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-6 max-w-md mx-auto">Pilih paket langganan untuk mulai menggunakan fitur premium ForMysha.</p>
                    <a href="{{ route('subscription.plans') }}" class="btn-primary text-sm min-h-[44px] inline-flex items-center">
                        💎 {{ __('Lihat Paket Langganan') }}
                    </a>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
