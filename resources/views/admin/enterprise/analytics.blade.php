<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                📊 {{ __('Analytics Enterprise') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-6">
            {{-- Sidebar --}}
            @include('admin.partials.sidebar')

            {{-- Main Content --}}
            <div class="flex-1 min-w-0">
                <x-breadcrumb :items="[
                    ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
                    ['label' => 'Enterprise'],
                    ['label' => 'Analytics'],
                ]" />

                {{-- Summary Cards --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    {{-- Active Users --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-6 border border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 rounded-xl bg-skyBlue-50 dark:bg-skyBlue-950/30 flex items-center justify-center">
                                <span class="text-xl">👥</span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Pengguna Aktif</p>
                                <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $summary['active_users'] }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Total Children --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-6 border border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 rounded-xl bg-softPink-50 dark:bg-softPink-950/30 flex items-center justify-center">
                                <span class="text-xl">👶</span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Total Anak</p>
                                <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $summary['total_children'] }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Total Media --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-6 border border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 rounded-xl bg-mintGreen-50 dark:bg-mintGreen-950/30 flex items-center justify-center">
                                <span class="text-xl">📷</span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Total Media</p>
                                <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ number_format($summary['total_media']) }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Storage Used --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-6 border border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 rounded-xl bg-lavender-50 dark:bg-lavender-950/30 flex items-center justify-center">
                                <span class="text-xl">💾</span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Penyimpanan</p>
                                <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ number_format($summary['storage_used_mb'], 1) }} MB</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- API Calls Today --}}
                <div class="bg-gradient-to-r from-skyBlue-50 to-lavender-50 dark:from-skyBlue-950/30 dark:to-lavender-950/30 rounded-2xl shadow-soft p-6 mb-6 border border-skyBlue-100 dark:border-skyBlue-900/30">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-semibold text-gray-800 dark:text-gray-100">API Calls Hari Ini</h3>
                            <p class="text-3xl font-bold text-skyBlue-600 dark:text-skyBlue-400">{{ number_format($summary['api_calls_today']) }}</p>
                        </div>
                        <div class="w-16 h-16 rounded-2xl bg-skyBlue-100 dark:bg-skyBlue-950/30 flex items-center justify-center">
                            <span class="text-3xl">🔌</span>
                        </div>
                    </div>
                </div>

                {{-- Date Range Filter --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-6 border border-gray-100 dark:border-gray-700 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-100">📊 Grafik Metrik</h3>
                        <div class="flex gap-2">
                            <a href="{{ route('enterprise.analytics', ['days' => 7]) }}"
                               class="px-3 py-1 rounded-lg text-sm font-medium transition-colors {{ $days == 7 ? 'bg-skyBlue-100 dark:bg-skyBlue-950/30 text-skyBlue-700 dark:text-skyBlue-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                                7 Hari
                            </a>
                            <a href="{{ route('enterprise.analytics', ['days' => 30]) }}"
                               class="px-3 py-1 rounded-lg text-sm font-medium transition-colors {{ $days == 30 ? 'bg-skyBlue-100 dark:bg-skyBlue-950/30 text-skyBlue-700 dark:text-skyBlue-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                                30 Hari
                            </a>
                            <a href="{{ route('enterprise.analytics', ['days' => 90]) }}"
                               class="px-3 py-1 rounded-lg text-sm font-medium transition-colors {{ $days == 90 ? 'bg-skyBlue-100 dark:bg-skyBlue-950/30 text-skyBlue-700 dark:text-skyBlue-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                                90 Hari
                            </a>
                        </div>
                    </div>

                    {{-- Metrics Table --}}
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-100 dark:border-gray-700">
                                    <th class="text-left py-3 px-4 font-semibold text-gray-600 dark:text-gray-400">Metrik</th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-600 dark:text-gray-400">Nilai Terakhir</th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-600 dark:text-gray-400">Total Data Points</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($metrics as $metricName => $metricData)
                                    <tr class="border-b border-gray-50 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="py-3 px-4">
                                            <span class="font-medium text-gray-800 dark:text-gray-100">{{ ucfirst(str_replace('_', ' ', $metricName)) }}</span>
                                        </td>
                                        <td class="py-3 px-4">
                                            @if ($metricData->isNotEmpty())
                                                <span class="font-semibold text-gray-800 dark:text-gray-100">{{ number_format($metricData->last()->value, 2) }}</span>
                                            @else
                                                <span class="text-gray-400 dark:text-gray-500">—</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="text-gray-600 dark:text-gray-300">{{ $metricData->count() }} data points</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if (collect($metrics)->every(fn ($m) => $m->isEmpty()))
                        <div class="text-center py-8">
                            <div class="text-4xl mb-3">📭</div>
                            <p class="text-gray-500 dark:text-gray-400">Belum ada data analytics untuk periode ini.</p>
                            <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Data akan muncul setelah ada aktivitas di platform.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
