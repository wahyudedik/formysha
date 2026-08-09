<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            📊 {{ __('Analytics') }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-6">
            {{-- Sidebar --}}
            @include('super-admin.partials.sidebar')

            {{-- Main Content --}}
            <div class="flex-1 min-w-0">
                <x-breadcrumb :items="[
                    ['label' => 'Dashboard', 'url' => route('super-admin.dashboard')],
                    ['label' => 'Analytics'],
                ]" />

                {{-- Stats Cards --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    {{-- Revenue Bulan Ini --}}
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
                        @if ($revenueLastMonth > 0)
                            @php $growth = round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1); @endphp
                            <div class="mt-3 text-xs {{ $growth >= 0 ? 'text-mintGreen-600 dark:text-mintGreen-400' : 'text-red-600 dark:text-red-400' }}">
                                {{ $growth >= 0 ? '↑' : '↓' }} {{ abs($growth) }}% dari bulan lalu
                            </div>
                        @endif
                    </div>

                    {{-- Total Revenue --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-5 border border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-skyBlue-50 dark:bg-skyBlue-950/30 flex items-center justify-center">
                                <span class="text-2xl">🏦</span>
                            </div>
                            <div>
                                <p class="text-lg font-bold text-gray-800 dark:text-gray-100">Rp {{ number_format($revenueTotal, 0, ',', '.') }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Total Pendapatan</p>
                            </div>
                        </div>
                    </div>

                    {{-- Active Tenants --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-5 border border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-lavender-50 dark:bg-lavender-950/30 flex items-center justify-center">
                                <span class="text-2xl">🏢</span>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $activeTenants }} / {{ $totalTenants }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Tenant Aktif</p>
                            </div>
                        </div>
                    </div>

                    {{-- Churn Rate --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-5 border border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-warmYellow-50 dark:bg-warmYellow-950/30 flex items-center justify-center">
                                <span class="text-2xl">📉</span>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $churnRate }}%</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Churn Rate (30 hari)</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    {{-- Revenue per Month --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft overflow-hidden">
                        <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="font-semibold text-gray-800 dark:text-gray-100">📈 {{ __('Pendapatan Bulanan') }}</h3>
                        </div>
                        <div class="p-4 sm:p-6">
                            <div class="space-y-3">
                                @forelse ($revenuePerMonth as $item)
                                    <div class="flex items-center gap-4">
                                        <span class="text-sm text-gray-600 dark:text-gray-300 w-24 shrink-0">{{ $item['month'] }}</span>
                                        <div class="flex-1">
                                            <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-4">
                                                @php
                                                    $maxRevenue = collect($revenuePerMonth)->max('amount');
                                                    $revPercentage = $maxRevenue > 0 ? ($item['amount'] / $maxRevenue) * 100 : 0;
                                                @endphp
                                                <div
                                                    class="h-4 rounded-full bg-gradient-to-r from-mintGreen-400 to-skyBlue-400 transition-all duration-500"
                                                    style="width: {{ $revPercentage }}%"
                                                ></div>
                                            </div>
                                        </div>
                                        <span class="text-sm font-medium text-gray-800 dark:text-gray-100 w-32 text-right shrink-0">
                                            Rp {{ number_format($item['amount'], 0, ',', '.') }}
                                        </span>
                                    </div>
                                @empty
                                    <div class="text-center py-8">
                                        <div class="text-4xl mb-3">📊</div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada data pendapatan.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- New Tenants per Month --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft overflow-hidden">
                        <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="font-semibold text-gray-800 dark:text-gray-100">📈 {{ __('Tenant Baru per Bulan') }}</h3>
                        </div>
                        <div class="p-4 sm:p-6">
                            <div class="space-y-3">
                                @forelse ($newTenantsPerMonth as $item)
                                    <div class="flex items-center gap-4">
                                        <span class="text-sm text-gray-600 dark:text-gray-300 w-24 shrink-0">{{ $item['month'] }}</span>
                                        <div class="flex-1">
                                            <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-4">
                                                @php
                                                    $maxTenants = collect($newTenantsPerMonth)->max('count');
                                                    $tenantPercentage = $maxTenants > 0 ? ($item['count'] / $maxTenants) * 100 : 0;
                                                @endphp
                                                <div
                                                    class="h-4 rounded-full bg-gradient-to-r from-lavender-400 to-softPink-400 transition-all duration-500"
                                                    style="width: {{ $tenantPercentage }}%"
                                                ></div>
                                            </div>
                                        </div>
                                        <span class="text-sm font-medium text-gray-800 dark:text-gray-100 w-16 text-right shrink-0">
                                            {{ $item['count'] }}
                                        </span>
                                    </div>
                                @empty
                                    <div class="text-center py-8">
                                        <div class="text-4xl mb-3">🏢</div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada data tenant.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Subscription Distribution --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft overflow-hidden">
                        <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="font-semibold text-gray-800 dark:text-gray-100">📊 {{ __('Distribusi Langganan') }}</h3>
                        </div>
                        <div class="p-4 sm:p-6">
                            <div class="space-y-3">
                                @php $totalSubs = collect($subscriptionDistribution)->sum('count'); @endphp
                                @foreach ($subscriptionDistribution as $item)
                                    @php
                                        $subPercentage = $totalSubs > 0 ? ($item['count'] / $totalSubs) * 100 : 0;
                                        $color = match($item['status']) {
                                            'Aktif' => 'from-mintGreen-400 to-skyBlue-400',
                                            'Pending' => 'from-warmYellow-400 to-peach-400',
                                            'Tidak Aktif' => 'from-gray-400 to-gray-500',
                                            'Dibatalkan' => 'from-red-400 to-red-500',
                                            'Jatuh Tempo' => 'from-softOrange-400 to-warmYellow-400',
                                            default => 'from-gray-400 to-gray-500',
                                        };
                                    @endphp
                                    <div class="flex items-center gap-4">
                                        <span class="text-sm text-gray-600 dark:text-gray-300 w-28 shrink-0">{{ $item['status'] }}</span>
                                        <div class="flex-1">
                                            <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-4">
                                                <div
                                                    class="h-4 rounded-full bg-gradient-to-r {{ $color }} transition-all duration-500"
                                                    style="width: {{ $subPercentage }}%"
                                                ></div>
                                            </div>
                                        </div>
                                        <span class="text-sm font-medium text-gray-800 dark:text-gray-100 w-16 text-right shrink-0">
                                            {{ $item['count'] }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Top Plans --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft overflow-hidden">
                        <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="font-semibold text-gray-800 dark:text-gray-100">🏆 {{ __('Paket Populer') }}</h3>
                        </div>
                        <div class="p-4 sm:p-6">
                            @if ($topPlans->isEmpty())
                                <div class="text-center py-8">
                                    <div class="text-4xl mb-3">📋</div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada data paket.</p>
                                </div>
                            @else
                                <div class="space-y-3">
                                    @foreach ($topPlans as $plan)
                                        <div class="flex items-center gap-4 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-lavender-400 to-skyBlue-400 flex items-center justify-center text-white text-sm font-bold shrink-0">
                                                {{ $loop->iteration }}
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $plan->name }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $plan->subscriptions_count }} langganan</p>
                                            </div>
                                            <span class="text-sm font-medium text-gray-800 dark:text-gray-100 shrink-0">
                                                {{ $plan->getPriceMonthlyFormatted() }}/bln
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Revenue by Plan --}}
                @if ($revenueByPlan->isNotEmpty())
                    <div class="mt-6 bg-white dark:bg-gray-800 rounded-2xl shadow-soft overflow-hidden">
                        <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="font-semibold text-gray-800 dark:text-gray-100">💰 {{ __('Pendapatan per Paket') }}</h3>
                        </div>
                        <div class="p-4 sm:p-6">
                            <div class="overflow-x-auto -mx-4 sm:mx-0 px-4 sm:px-0">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="border-b border-gray-100 dark:border-gray-700">
                                            <th class="text-left px-4 py-3 font-semibold text-gray-600 dark:text-gray-300">Paket</th>
                                            <th class="text-right px-4 py-3 font-semibold text-gray-600 dark:text-gray-300">Total Pendapatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($revenueByPlan as $item)
                                            <tr class="border-b border-gray-50 dark:border-gray-700/50">
                                                <td class="px-4 py-3 text-gray-800 dark:text-gray-100 font-medium">{{ $item->name }}</td>
                                                <td class="px-4 py-3 text-gray-800 dark:text-gray-100 text-right">Rp {{ number_format($item->total_revenue, 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
