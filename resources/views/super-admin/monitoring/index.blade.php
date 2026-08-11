<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            🔍 {{ __('navigation.monitoring') }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-6">
            {{-- Sidebar --}}
            @include('super-admin.partials.sidebar')

            {{-- Main Content --}}
            <div class="flex-1 min-w-0">
                <x-breadcrumb :items="[
                    ['label' => __('navigation.dashboard'), 'url' => route('super-admin.dashboard')],
                    ['label' => __('navigation.monitoring')],
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
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('super_admin.active_tenants') }}</p>
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
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('super_admin.total_users') }}</p>
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
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('super_admin.total_media') }}</p>
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
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('empty_states.no_recent_errors_short') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- System Health --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft overflow-hidden mb-6">
                    <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-100">🏥 {{ __('monitoring.system_health') }}</h3>
                    </div>
                    <div class="p-4 sm:p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {{-- Database --}}
                            <div class="flex items-center gap-4 p-4 rounded-xl border border-gray-100 dark:border-gray-700">
                                <div class="w-12 h-12 rounded-xl {{ $databaseHealthy['status'] === 'healthy' ? 'bg-mintGreen-50 dark:bg-mintGreen-950/30' : 'bg-red-50 dark:bg-red-950/30' }} flex items-center justify-center">
                                    <span class="text-2xl">{{ $databaseHealthy['status'] === 'healthy' ? '✅' : '❌' }}</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800 dark:text-gray-100">{{ __('monitoring.system_health') }} — Database</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $databaseHealthy['message'] }}</p>
                                    @if (isset($databaseHealthy['latency']))
                                        <p class="text-xs text-gray-400 dark:text-gray-500">{{ __('monitoring.latency') }} {{ $databaseHealthy['latency'] }}</p>
                                    @endif
                                </div>
                            </div>

                            {{-- Cache --}}
                            <div class="flex items-center gap-4 p-4 rounded-xl border border-gray-100 dark:border-gray-700">
                                <div class="w-12 h-12 rounded-xl {{ $cacheHealthy['status'] === 'healthy' ? 'bg-mintGreen-50 dark:bg-mintGreen-950/30' : 'bg-red-50 dark:bg-red-950/30' }} flex items-center justify-center">
                                    <span class="text-2xl">{{ $cacheHealthy['status'] === 'healthy' ? '✅' : '❌' }}</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800 dark:text-gray-100">{{ __('monitoring.system_health') }} — Cache</p>
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
                            <h3 class="font-semibold text-gray-800 dark:text-gray-100">🔐 {{ __('monitoring.recent_logins') }}</h3>
                        </div>
                        <div class="p-4 sm:p-6">
                            @if ($recentLogins->isEmpty())
                                <div class="text-center py-8">
                                    <div class="text-4xl mb-3">🔐</div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('empty_states.no_login_activity') }}</p>
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
                            <h3 class="font-semibold text-gray-800 dark:text-gray-100">⚠️ {{ __('monitoring.recent_errors') }}</h3>
                        </div>
                        <div class="p-4 sm:p-6">
                            @if ($recentErrors->isEmpty())
                                <div class="text-center py-8">
                                    <div class="text-4xl mb-3">✅</div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('empty_states.no_recent_errors') }}</p>
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
                        <h3 class="font-semibold text-gray-800 dark:text-gray-100">💾 {{ __('monitoring.storage_usage') }}</h3>
                    </div>
                    <div class="p-4 sm:p-6">
                        @if ($tenantStorage->isEmpty())
                            <div class="text-center py-8">
                                <div class="text-4xl mb-3">💾</div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('empty_states.no_storage_data') }}</p>
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
                                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $tenant['plan_name'] }} · {{ $tenant['children_count'] }} {{ __('monitoring.children_count') }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- B2B Monitoring Section --}}
                @if ($b2bTenantCount > 0)
                    <div class="mt-8">
                        <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">🏥 {{ __('monitoring.b2b_monitoring') }}</h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                            {{-- B2B Tenants --}}
                            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-5 border border-gray-100 dark:border-gray-700">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-xl bg-softPink-50 dark:bg-softPink-950/30 flex items-center justify-center">
                                        <span class="text-2xl">🏥</span>
                                    </div>
                                    <div>
                                        <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $b2bTenantCount }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('super_admin.active_facilities') }}</p>
                                    </div>
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
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('super_admin.active_staff') }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Pending Referrals --}}
                            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-5 border border-gray-100 dark:border-gray-700">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-xl {{ $pendingReferrals > 0 ? 'bg-warmYellow-50 dark:bg-warmYellow-950/30' : 'bg-mintGreen-50 dark:bg-mintGreen-950/30' }} flex items-center justify-center">
                                        <span class="text-2xl">{{ $pendingReferrals > 0 ? '⚠️' : '✅' }}</span>
                                    </div>
                                    <div>
                                        <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $pendingReferrals }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('super_admin.pending_referrals') }}</p>
                                    </div>
                                </div>
                                <div class="mt-3 text-xs text-gray-500 dark:text-gray-400">{{ $totalReferrals }} {{ __('super_admin.total_referrals') }}</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            {{-- Facilities by Staff --}}
                            @if ($b2bFacilities->isNotEmpty())
                                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft overflow-hidden">
                                    <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-gray-700">
                                        <h3 class="font-semibold text-gray-800 dark:text-gray-100">👨‍⚕️ {{ __('monitoring.top_facilities_by_staff') }}</h3>
                                    </div>
                                    <div class="p-4 sm:p-6">
                                        <div class="space-y-3">
                                            @foreach ($b2bFacilities as $facility)
                                                <a href="{{ route('super-admin.tenants.show', $facility) }}" class="block p-3 rounded-xl hover:bg-softPink-50 dark:hover:bg-softPink-950/20 transition">
                                                    <div class="flex items-center justify-between">
                                                        <div class="flex items-center gap-3 min-w-0">
                                                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-softPink-400 to-lavender-400 flex items-center justify-center text-white text-sm font-bold shrink-0">
                                                                {{ strtoupper(substr($facility->name, 0, 1)) }}
                                                            </div>
                                                            <div class="min-w-0">
                                                                <p class="text-sm font-medium text-gray-800 dark:text-gray-100 truncate">{{ $facility->name }}</p>
                                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $facility->getTypeLabel() }}</p>
                                                            </div>
                                                        </div>
                                                        <span class="text-sm font-bold text-gray-800 dark:text-gray-100 shrink-0">{{ $facility->staff_count }} {{ __('super_admin.staff_count') }}</span>
                                                    </div>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Facilities by Clinical Notes --}}
                            @if ($topFacilitiesByNotes->isNotEmpty())
                                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft overflow-hidden">
                                    <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-gray-700">
                                        <h3 class="font-semibold text-gray-800 dark:text-gray-100">📋 {{ __('monitoring.top_facilities_by_notes') }}</h3>
                                    </div>
                                    <div class="p-4 sm:p-6">
                                        <div class="space-y-3">
                                            @foreach ($topFacilitiesByNotes as $facility)
                                                <a href="{{ route('super-admin.tenants.show', $facility) }}" class="block p-3 rounded-xl hover:bg-lavender-50 dark:hover:bg-lavender-950/20 transition">
                                                    <div class="flex items-center justify-between">
                                                        <div class="flex items-center gap-3 min-w-0">
                                                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-lavender-400 to-skyBlue-400 flex items-center justify-center text-white text-sm font-bold shrink-0">
                                                                {{ strtoupper(substr($facility->name, 0, 1)) }}
                                                            </div>
                                                            <div class="min-w-0">
                                                                <p class="text-sm font-medium text-gray-800 dark:text-gray-100 truncate">{{ $facility->name }}</p>
                                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $facility->getTypeLabel() }}</p>
                                                            </div>
                                                        </div>
                                                        <span class="text-sm font-bold text-gray-800 dark:text-gray-100 shrink-0">{{ $facility->clinical_notes_count }} {{ __('monitoring.notes_count') }}</span>
                                                    </div>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Facilities with Pending Referrals --}}
                            @if ($facilitiesWithPendingReferrals->isNotEmpty())
                                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft overflow-hidden lg:col-span-2">
                                    <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-gray-700">
                                        <h3 class="font-semibold text-gray-800 dark:text-gray-100">🔄 {{ __('monitoring.facilities_pending_referrals') }}</h3>
                                    </div>
                                    <div class="p-4 sm:p-6">
                                        <div class="overflow-x-auto -mx-4 sm:mx-0 px-4 sm:px-0">
                                            <table class="w-full text-sm">
                                                <thead>
                                                    <tr class="border-b border-gray-100 dark:border-gray-700">
                                                        <th class="text-left px-4 py-3 font-semibold text-gray-600 dark:text-gray-300">{{ __('monitoring.facility') }}</th>
                                                        <th class="text-left px-4 py-3 font-semibold text-gray-600 dark:text-gray-300">{{ __('monitoring.type') }}</th>
                                                        <th class="text-right px-4 py-3 font-semibold text-gray-600 dark:text-gray-300">{{ __('common.pending') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($facilitiesWithPendingReferrals as $facility)
                                                        <tr class="border-b border-gray-50 dark:border-gray-700/50">
                                                            <td class="px-4 py-3">
                                                                <a href="{{ route('super-admin.tenants.show', $facility) }}" class="text-gray-800 dark:text-gray-100 font-medium hover:text-skyBlue-600 dark:hover:text-skyBlue-400 transition">
                                                                    {{ $facility->name }}
                                                                </a>
                                                            </td>
                                                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $facility->getTypeLabel() }}</td>
                                                            <td class="px-4 py-3 text-right">
                                                                <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium {{ $facility->pending_referrals_count > 0 ? 'bg-warmYellow-100 text-warmYellow-600 dark:bg-warmYellow-950/30 dark:text-warmYellow-400' : 'bg-mintGreen-100 text-mintGreen-600 dark:bg-mintGreen-950/30 dark:text-mintGreen-400' }}">
                                                                    {{ $facility->pending_referrals_count }}
                                                                </span>
                                                            </td>
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
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
