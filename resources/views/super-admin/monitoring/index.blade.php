<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            🔍 {{ __('Monitoring') }}
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
                    ['label' => 'Monitoring'],
                ]" />

                {{-- Stats Cards --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    {{-- Active Tenants --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-5 border border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-skyBlue-50 dark:bg-skyBlue-950/30 flex items-center justify-center">
                                <span class="text-2xl">🏢</span>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $activeTenants }} / {{ $totalTenants }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Tenant Aktif</p>
                            </div>
                        </div>
                    </div>

                    {{-- Total Users --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-5 border border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-mintGreen-50 dark:bg-mintGreen-950/30 flex items-center justify-center">
                                <span class="text-2xl">👥</span>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $totalUsers }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Total Pengguna</p>
                            </div>
                        </div>
                    </div>

                    {{-- Total Media --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-5 border border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-lavender-50 dark:bg-lavender-950/30 flex items-center justify-center">
                                <span class="text-2xl">📁</span>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $totalMedia }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Total Media</p>
                            </div>
                        </div>
                        <div class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                            Total: {{ number_format(round($totalMediaSize / 1048576, 2), 2) }} MB
                        </div>
                    </div>

                    {{-- Error Count --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-5 border border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl {{ $recentErrors->isEmpty() ? 'bg-mintGreen-50 dark:bg-mintGreen-950/30' : 'bg-red-50 dark:bg-red-950/30' }} flex items-center justify-center">
                                <span class="text-2xl">{{ $recentErrors->isEmpty() ? '✅' : '⚠️' }}</span>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $recentErrors->count() }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Error Terbaru</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- System Health --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft overflow-hidden mb-6">
                    <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-100">🏥 {{ __('Kesehatan Sistem') }}</h3>
                    </div>
                    <div class="p-4 sm:p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {{-- Database --}}
                            <div class="flex items-center gap-4 p-4 rounded-xl border border-gray-100 dark:border-gray-700">
                                <div class="w-12 h-12 rounded-xl {{ $databaseHealthy['status'] === 'healthy' ? 'bg-mintGreen-50 dark:bg-mintGreen-950/30' : 'bg-red-50 dark:bg-red-950/30' }} flex items-center justify-center">
                                    <span class="text-2xl">{{ $databaseHealthy['status'] === 'healthy' ? '✅' : '❌' }}</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800 dark:text-gray-100">Database</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $databaseHealthy['message'] }}</p>
                                    @if (isset($databaseHealthy['latency']))
                                        <p class="text-xs text-gray-400 dark:text-gray-500">Latency: {{ $databaseHealthy['latency'] }}</p>
                                    @endif
                                </div>
                            </div>

                            {{-- Cache --}}
                            <div class="flex items-center gap-4 p-4 rounded-xl border border-gray-100 dark:border-gray-700">
                                <div class="w-12 h-12 rounded-xl {{ $cacheHealthy['status'] === 'healthy' ? 'bg-mintGreen-50 dark:bg-mintGreen-950/30' : 'bg-red-50 dark:bg-red-950/30' }} flex items-center justify-center">
                                    <span class="text-2xl">{{ $cacheHealthy['status'] === 'healthy' ? '✅' : '❌' }}</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800 dark:text-gray-100">Cache</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $cacheHealthy['message'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    {{-- Recent Login Activity --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft overflow-hidden">
                        <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="font-semibold text-gray-800 dark:text-gray-100">🔐 {{ __('Aktivitas Login Terbaru') }}</h3>
                        </div>
                        <div class="p-4 sm:p-6">
                            @if ($recentLogins->isEmpty())
                                <div class="text-center py-8">
                                    <div class="text-4xl mb-3">🔐</div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada aktivitas login.</p>
                                </div>
                            @else
                                <div class="space-y-3">
                                    @foreach ($recentLogins as $log)
                                        <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                            <div class="w-8 h-8 rounded-lg bg-skyBlue-50 dark:bg-skyBlue-950/30 flex items-center justify-center text-sm shrink-0">
                                                🔑
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-sm font-medium text-gray-800 dark:text-gray-100 truncate">{{ $log->user->name ?? 'Unknown' }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $log->tenant->name ?? '-' }} · {{ $log->created_at->locale('id')->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Error Log Summary --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft overflow-hidden">
                        <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="font-semibold text-gray-800 dark:text-gray-100">⚠️ {{ __('Log Error Terbaru') }}</h3>
                        </div>
                        <div class="p-4 sm:p-6">
                            @if ($recentErrors->isEmpty())
                                <div class="text-center py-8">
                                    <div class="text-4xl mb-3">✅</div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada error terbaru.</p>
                                </div>
                            @else
                                <div class="space-y-3">
                                    @foreach ($recentErrors as $error)
                                        <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-red-50 dark:hover:bg-red-950/20 transition">
                                            <div class="w-8 h-8 rounded-lg bg-red-50 dark:bg-red-950/30 flex items-center justify-center text-sm shrink-0">
                                                ⚠️
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-sm font-medium text-gray-800 dark:text-gray-100 truncate">{{ $error->event }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $error->user->name ?? 'Unknown' }} · {{ $error->created_at->locale('id')->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Storage Usage per Tenant --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft overflow-hidden">
                    <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-100">💾 {{ __('Penggunaan Penyimpanan per Tenant') }}</h3>
                    </div>
                    <div class="p-4 sm:p-6">
                        @if ($tenantStorage->isEmpty())
                            <div class="text-center py-8">
                                <div class="text-4xl mb-3">💾</div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada data penyimpanan.</p>
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach ($tenantStorage as $tenant)
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-skyBlue-400 to-lavender-400 flex items-center justify-center text-white text-sm font-bold shrink-0">
                                            {{ strtoupper(substr($tenant['name'], 0, 1)) }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between mb-1">
                                                <p class="text-sm font-medium text-gray-800 dark:text-gray-100 truncate">{{ $tenant['name'] }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 shrink-0 ml-2">{{ $tenant['storage_formatted'] }} / {{ $tenant['storage_limit_formatted'] }}</p>
                                            </div>
                                            @if ($tenant['storage_limit'] > 0 && $tenant['storage_limit'] !== PHP_INT_MAX)
                                                <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2">
                                                    <div
                                                        class="h-2 rounded-full bg-gradient-to-r from-mintGreen-400 to-skyBlue-400 transition-all duration-500 {{ $tenant['storage_percentage'] > 80 ? 'from-softOrange-400 to-red-400' : '' }}"
                                                        style="width: {{ $tenant['storage_percentage'] }}%"
                                                    ></div>
                                                </div>
                                            @endif
                                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $tenant['plan_name'] }} · {{ $tenant['children_count'] }} anak</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
