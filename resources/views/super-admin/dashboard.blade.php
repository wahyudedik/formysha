<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            🛡️ {{ __('Super Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-6">
            {{-- Sidebar --}}
            @include('super-admin.partials.sidebar')

            {{-- Main Content --}}
            <div class="flex-1 min-w-0">
                {{-- Stats Cards --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    {{-- Total Tenants --}}
                    <div class="bg-white rounded-2xl shadow-soft p-5 border border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-skyBlue-50 flex items-center justify-center">
                                <span class="text-2xl">🏢</span>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-800">{{ $totalTenants }}</p>
                                <p class="text-xs text-gray-500">Total Tenant</p>
                            </div>
                        </div>
                        <div class="mt-3 text-xs text-mintGreen-600">
                            {{ $activeTenants }} aktif
                        </div>
                    </div>

                    {{-- Pending Payments --}}
                    <div class="bg-white rounded-2xl shadow-soft p-5 border border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-warmYellow-50 flex items-center justify-center">
                                <span class="text-2xl">⏳</span>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-800">{{ $pendingPayments }}</p>
                                <p class="text-xs text-gray-500">Menunggu Verifikasi</p>
                            </div>
                        </div>
                        <div class="mt-3 text-xs text-gray-500">
                            {{ $totalPayments }} total transaksi
                        </div>
                    </div>

                    {{-- Revenue This Month --}}
                    <div class="bg-white rounded-2xl shadow-soft p-5 border border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-mintGreen-50 flex items-center justify-center">
                                <span class="text-2xl">💰</span>
                            </div>
                            <div>
                                <p class="text-lg font-bold text-gray-800">Rp {{ number_format($revenueThisMonth, 0, ',', '.') }}</p>
                                <p class="text-xs text-gray-500">Pendapatan Bulan Ini</p>
                            </div>
                        </div>
                        <div class="mt-3 text-xs text-gray-500">
                            Total: Rp {{ number_format($revenueTotal, 0, ',', '.') }}
                        </div>
                    </div>

                    {{-- Total Plans --}}
                    <div class="bg-white rounded-2xl shadow-soft p-5 border border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-lavender-50 flex items-center justify-center">
                                <span class="text-2xl">📋</span>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-800">{{ $totalPlans }}</p>
                                <p class="text-xs text-gray-500">Total Paket</p>
                            </div>
                        </div>
                        <div class="mt-3 text-xs text-gray-500">
                            {{ $approvedPayments }} pembayaran disetujui
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Recent Pending Payments --}}
                    <div class="bg-white rounded-2xl shadow-soft overflow-hidden">
                        <div class="p-6 border-b border-gray-100">
                            <div class="flex items-center justify-between">
                                <h3 class="font-semibold text-gray-800">⏳ {{ __('Pembayaran Pending') }}</h3>
                                <a href="{{ route('super-admin.payments.index') }}" class="text-sm text-skyBlue-600 hover:text-skyBlue-700 transition">
                                    Lihat Semua →
                                </a>
                            </div>
                        </div>
                        <div class="p-6">
                            @if ($recentPayments->isEmpty())
                                <div class="text-center py-8">
                                    <div class="text-4xl mb-3">✅</div>
                                    <p class="text-sm text-gray-500">Tidak ada pembayaran pending.</p>
                                </div>
                            @else
                                <div class="space-y-3">
                                    @foreach ($recentPayments as $payment)
                                        <a href="{{ route('super-admin.payments.show', $payment) }}" class="block p-3 rounded-xl hover:bg-warmYellow-50 transition">
                                            <div class="flex items-center justify-between">
                                                <div class="min-w-0">
                                                    <p class="text-sm font-medium text-gray-800 truncate">{{ $payment->tenant->name ?? '-' }}</p>
                                                    <p class="text-xs text-gray-500">{{ $payment->bank_name ?? '-' }} · {{ $payment->created_at->locale('id')->diffForHumans() }}</p>
                                                </div>
                                                <span class="text-sm font-bold text-gray-800 shrink-0">{{ $payment->getAmountFormatted() }}</span>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Recent Tenants --}}
                    <div class="bg-white rounded-2xl shadow-soft overflow-hidden">
                        <div class="p-6 border-b border-gray-100">
                            <div class="flex items-center justify-between">
                                <h3 class="font-semibold text-gray-800">🏢 {{ __('Tenant Terbaru') }}</h3>
                                <a href="{{ route('super-admin.tenants.index') }}" class="text-sm text-skyBlue-600 hover:text-skyBlue-700 transition">
                                    Lihat Semua →
                                </a>
                            </div>
                        </div>
                        <div class="p-6">
                            @if ($recentTenants->isEmpty())
                                <div class="text-center py-8">
                                    <div class="text-4xl mb-3">🏢</div>
                                    <p class="text-sm text-gray-500">Belum ada tenant.</p>
                                </div>
                            @else
                                <div class="space-y-3">
                                    @foreach ($recentTenants as $tenant)
                                        <a href="{{ route('super-admin.tenants.show', $tenant) }}" class="block p-3 rounded-xl hover:bg-skyBlue-50 transition">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-3 min-w-0">
                                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-skyBlue-400 to-lavender-400 flex items-center justify-center text-white text-sm font-bold shrink-0">
                                                        {{ strtoupper(substr($tenant->name, 0, 1)) }}
                                                    </div>
                                                    <div class="min-w-0">
                                                        <p class="text-sm font-medium text-gray-800 truncate">{{ $tenant->name }}</p>
                                                        <p class="text-xs text-gray-500">{{ $tenant->users_count ?? 0 }} pengguna · {{ $tenant->children_count ?? 0 }} anak</p>
                                                    </div>
                                                </div>
                                                <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium {{ $tenant->is_active ? 'bg-mintGreen-100 text-mintGreen-600' : 'bg-red-100 text-red-600' }}">
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
                <div class="mt-6 bg-white rounded-2xl shadow-soft p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">🚀 {{ __('Aksi Cepat') }}</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <a href="{{ route('super-admin.tenants.create') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl bg-skyBlue-50 hover:bg-skyBlue-100 transition text-center">
                            <span class="text-2xl">➕</span>
                            <span class="text-xs font-medium text-skyBlue-700">Tambah Tenant</span>
                        </a>
                        <a href="{{ route('super-admin.plans.create') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl bg-lavender-50 hover:bg-lavender-100 transition text-center">
                            <span class="text-2xl">📋</span>
                            <span class="text-xs font-medium text-lavender-700">Tambah Paket</span>
                        </a>
                        <a href="{{ route('super-admin.payments.index') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl bg-warmYellow-50 hover:bg-warmYellow-100 transition text-center">
                            <span class="text-2xl">💳</span>
                            <span class="text-xs font-medium text-warmYellow-700">Verifikasi Bayar</span>
                        </a>
                        <a href="{{ route('super-admin.audit-logs.index') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl bg-peach-50 hover:bg-peach-100 transition text-center">
                            <span class="text-2xl">📜</span>
                            <span class="text-xs font-medium text-peach-700">Audit Log</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
