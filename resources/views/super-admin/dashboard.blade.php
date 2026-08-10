<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                🛡️ {{ __('Super Admin Dashboard') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-6">
            {{-- Sidebar --}}
            @include('super-admin.partials.sidebar')

            {{-- Main Content --}}
            <div class="flex-1 min-w-0">
                {{-- Stats Cards --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    {{-- Total Tenants --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-5 border border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-skyBlue-50 dark:bg-skyBlue-950/30 flex items-center justify-center">
                                <span class="text-2xl">🏢</span>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $totalTenants }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Total Tenant</p>
                            </div>
                        </div>
                        <div class="mt-3 text-xs text-mintGreen-600 dark:text-mintGreen-400">
                            {{ $activeTenants }} aktif
                        </div>
                    </div>

                    {{-- Pending Payments --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-5 border border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-warmYellow-50 dark:bg-warmYellow-950/30 flex items-center justify-center">
                                <span class="text-2xl">⏳</span>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $pendingPayments }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Menunggu Verifikasi</p>
                            </div>
                        </div>
                        <div class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                            {{ $totalPayments }} total transaksi
                        </div>
                    </div>

                    {{-- Revenue This Month --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-5 border border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-mintGreen-50 dark:bg-mintGreen-950/30 flex items-center justify-center">
                                <span class="text-2xl">💰</span>
                            </div>
                            <div>
                                <p class="text-lg font-bold text-gray-800 dark:text-gray-100">Rp {{ number_format($revenueThisMonth, 0, ',', '.') }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Pendapatan Bulan Ini</p>
                            </div>
                        </div>
                        <div class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                            Total: Rp {{ number_format($revenueTotal, 0, ',', '.') }}
                        </div>
                    </div>

                    {{-- Total Plans --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-5 border border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-lavender-50 dark:bg-lavender-950/30 flex items-center justify-center">
                                <span class="text-2xl">📋</span>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $totalPlans }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Total Paket</p>
                            </div>
                        </div>
                        <div class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                            {{ $approvedPayments }} pembayaran disetujui
                        </div>
                    </div>
                </div>

                {{-- B2B Stats Cards --}}
                @if ($b2bTenantCount > 0)
                    <div class="mb-8">
                        <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">🏥 {{ __('Statistik B2B (Fasilitas)') }}</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            {{-- B2B Tenants --}}
                            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-5 border border-gray-100 dark:border-gray-700">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-xl bg-softPink-50 dark:bg-softPink-950/30 flex items-center justify-center">
                                        <span class="text-2xl">🏥</span>
                                    </div>
                                    <div>
                                        <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $b2bTenantCount }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Fasilitas B2B</p>
                                    </div>
                                </div>
                                <div class="mt-3 text-xs text-mintGreen-600 dark:text-mintGreen-400">
                                    {{ $b2bActiveCount }} aktif
                                </div>
                            </div>

                            {{-- Total Staff --}}
                            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-5 border border-gray-100 dark:border-gray-700">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-xl bg-skyBlue-50 dark:bg-skyBlue-950/30 flex items-center justify-center">
                                        <span class="text-2xl">👨‍⚕️</span>
                                    </div>
                                    <div>
                                        <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $totalStaff }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Staf Aktif</p>
                                    </div>
                                </div>
                                <div class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                                    {{ $totalPatientLinks }} tautan pasien
                                </div>
                            </div>

                            {{-- Clinical Notes This Month --}}
                            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-5 border border-gray-100 dark:border-gray-700">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-xl bg-lavender-50 dark:bg-lavender-950/30 flex items-center justify-center">
                                        <span class="text-2xl">📋</span>
                                    </div>
                                    <div>
                                        <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $clinicalNotesThisMonth }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Catatan Klinis (Bulan Ini)</p>
                                    </div>
                                </div>
                                <div class="mt-3 text-xs {{ $pendingReferrals > 0 ? 'text-warmYellow-600 dark:text-warmYellow-400' : 'text-gray-500 dark:text-gray-400' }}">
                                    {{ $pendingReferrals }} rujukan pending
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- B2B Revenue Breakdown --}}
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-5 border border-gray-100 dark:border-gray-700">
                            <h3 class="font-semibold text-gray-800 dark:text-gray-100 mb-4">💰 {{ __('Pendapatan B2B vs B2C') }}</h3>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 rounded-full bg-softPink-400"></div>
                                        <span class="text-sm text-gray-600 dark:text-gray-300">B2B (Fasilitas)</span>
                                    </div>
                                    <span class="text-sm font-bold text-gray-800 dark:text-gray-100">Rp {{ number_format($revenueB2B, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 rounded-full bg-skyBlue-400"></div>
                                        <span class="text-sm text-gray-600 dark:text-gray-300">B2C (Keluarga)</span>
                                    </div>
                                    <span class="text-sm font-bold text-gray-800 dark:text-gray-100">Rp {{ number_format($revenueB2C, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Recent Pending Payments --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft overflow-hidden">
                        <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-gray-700">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                <h3 class="font-semibold text-gray-800 dark:text-gray-100">⏳ {{ __('Pembayaran Pending') }}</h3>
                                <a href="{{ route('super-admin.payments.index') }}" class="text-sm text-skyBlue-600 hover:text-skyBlue-700 dark:text-skyBlue-400 dark:hover:text-skyBlue-300 transition min-h-[44px] inline-flex items-center">
                                    Lihat Semua →
                                </a>
                            </div>
                        </div>
                        <div class="p-4 sm:p-6">
                            @if ($recentPayments->isEmpty())
                                <div class="text-center py-8">
                                    <div class="text-4xl mb-3">✅</div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada pembayaran pending.</p>
                                </div>
                            @else
                                <div class="space-y-3">
                                    @foreach ($recentPayments as $payment)
                                        <a href="{{ route('super-admin.payments.show', $payment) }}" class="block p-3 rounded-xl hover:bg-warmYellow-50 dark:hover:bg-warmYellow-950/20 transition">
                                            <div class="flex items-center justify-between">
                                                <div class="min-w-0">
                                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-100 truncate">{{ $payment->tenant->name ?? '-' }}</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $payment->bank_name ?? '-' }} · {{ $payment->created_at->locale('id')->diffForHumans() }}</p>
                                                </div>
                                                <span class="text-sm font-bold text-gray-800 dark:text-gray-100 shrink-0">{{ $payment->getAmountFormatted() }}</span>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Recent Tenants --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft overflow-hidden">
                        <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-gray-700">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                <h3 class="font-semibold text-gray-800 dark:text-gray-100">🏢 {{ __('Tenant Terbaru') }}</h3>
                                <a href="{{ route('super-admin.tenants.index') }}" class="text-sm text-skyBlue-600 hover:text-skyBlue-700 dark:text-skyBlue-400 dark:hover:text-skyBlue-300 transition min-h-[44px] inline-flex items-center">
                                    Lihat Semua →
                                </a>
                            </div>
                        </div>
                        <div class="p-4 sm:p-6">
                            @if ($recentTenants->isEmpty())
                                <div class="text-center py-8">
                                    <div class="text-4xl mb-3">🏢</div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada tenant.</p>
                                </div>
                            @else
                                <div class="space-y-3">
                                    @foreach ($recentTenants as $tenant)
                                        <a href="{{ route('super-admin.tenants.show', $tenant) }}" class="block p-3 rounded-xl hover:bg-skyBlue-50 dark:hover:bg-skyBlue-950/20 transition">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-3 min-w-0">
                                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-skyBlue-400 to-lavender-400 flex items-center justify-center text-white text-sm font-bold shrink-0">
                                                        {{ strtoupper(substr($tenant->name, 0, 1)) }}
                                                    </div>
                                                    <div class="min-w-0">
                                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-100 truncate">{{ $tenant->name }}</p>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $tenant->users_count ?? 0 }} pengguna · {{ $tenant->children_count ?? 0 }} anak</p>
                                                    </div>
                                                </div>
                                                <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium {{ $tenant->is_active ? 'bg-mintGreen-100 text-mintGreen-600 dark:bg-mintGreen-950/30 dark:text-mintGreen-400' : 'bg-red-100 text-red-600 dark:bg-red-950/30 dark:text-red-400' }}">
                                                    {{ $tenant->is_active ? 'Aktif' : 'Nonaktif' }}
                                                </span>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="mt-6 bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-4 sm:p-6">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-100 mb-4">🚀 {{ __('Aksi Cepat') }}</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <a href="{{ route('super-admin.tenants.create') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl bg-skyBlue-50 dark:bg-skyBlue-950/30 hover:bg-skyBlue-100 dark:hover:bg-skyBlue-950/50 transition text-center min-h-[44px] justify-center">
                            <span class="text-2xl">➕</span>
                            <span class="text-xs font-medium text-skyBlue-700 dark:text-skyBlue-400">Tambah Tenant</span>
                        </a>
                        <a href="{{ route('super-admin.plans.create') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl bg-lavender-50 dark:bg-lavender-950/30 hover:bg-lavender-100 dark:hover:bg-lavender-950/50 transition text-center min-h-[44px] justify-center">
                            <span class="text-2xl">📋</span>
                            <span class="text-xs font-medium text-lavender-700 dark:text-lavender-400">Tambah Paket</span>
                        </a>
                        <a href="{{ route('super-admin.payments.index') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl bg-warmYellow-50 dark:bg-warmYellow-950/30 hover:bg-warmYellow-100 dark:hover:bg-warmYellow-950/50 transition text-center min-h-[44px] justify-center">
                            <span class="text-2xl">💳</span>
                            <span class="text-xs font-medium text-warmYellow-700 dark:text-warmYellow-400">Verifikasi Bayar</span>
                        </a>
                        <a href="{{ route('super-admin.audit-logs.index') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl bg-peach-50 dark:bg-peach-950/30 hover:bg-peach-100 dark:hover:bg-peach-950/50 transition text-center min-h-[44px] justify-center">
                            <span class="text-2xl">📜</span>
                            <span class="text-xs font-medium text-peach-700 dark:text-peach-400">Audit Log</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
