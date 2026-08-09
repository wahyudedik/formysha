<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('children.show', $child) }}" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                    📏 {{ __('My Growth') }} — {{ $child->nickname ?? $child->name }}
                </h2>
            </div>
            <a href="{{ route('growth.create', $child) }}" class="btn-primary text-sm min-h-[44px]">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('Tambah Pengukuran') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 p-4 bg-mintGreen-50 dark:bg-mintGreen-950/30 border border-mintGreen-200 dark:border-mintGreen-800 text-mintGreen-700 dark:text-mintGreen-400 rounded-xl" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Latest Growth Summary -->
            @if ($latestGrowth)
                @php
                    $statusColors = [
                        'normal' => ['bg' => 'bg-mintGreen-50 dark:bg-mintGreen-950/30', 'border' => 'border-mintGreen-200 dark:border-mintGreen-800', 'text' => 'text-mintGreen-700 dark:text-mintGreen-400', 'label' => 'Normal'],
                        'below_normal' => ['bg' => 'bg-warmYellow-50 dark:bg-warmYellow-950/30', 'border' => 'border-warmYellow-200 dark:border-warmYellow-800', 'text' => 'text-warmYellow-700 dark:text-warmYellow-400', 'label' => 'Di Bawah Normal'],
                        'above_normal' => ['bg' => 'bg-softOrange-50 dark:bg-softOrange-950/30', 'border' => 'border-softOrange-200 dark:border-softOrange-800', 'text' => 'text-softOrange-700 dark:text-softOrange-400', 'label' => 'Di Atas Normal'],
                        'unknown' => ['bg' => 'bg-gray-50 dark:bg-gray-700', 'border' => 'border-gray-200 dark:border-gray-600', 'text' => 'text-gray-500 dark:text-gray-400', 'label' => 'Tidak Diketahui'],
                    ];
                @endphp
                <div class="mb-8 grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-2xl p-5">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-softPink-100 to-softPink-50 dark:from-softPink-950/30 dark:to-softPink-950/30 flex items-center justify-center text-2xl">
                                ⚖️
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Berat Badan') }}</p>
                                <p class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ $latestGrowth->weight_label ?? '—' }}</p>
                                @if ($assessment && isset($assessment['weightStatus']))
                                    @php $sc = $statusColors[$assessment['weightStatus']] ?? $statusColors['unknown']; @endphp
                                    <span class="inline-block mt-1 px-2 py-0.5 text-[10px] font-medium rounded-full {{ $sc['bg'] }} {{ $sc['border'] }} {{ $sc['text'] }} border">
                                        {{ $sc['label'] }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-2xl p-5">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-skyBlue-100 to-skyBlue-50 dark:from-skyBlue-950/30 dark:to-skyBlue-950/30 flex items-center justify-center text-2xl">
                                📐
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Tinggi Badan') }}</p>
                                <p class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ $latestGrowth->height_label ?? '—' }}</p>
                                @if ($assessment && isset($assessment['heightStatus']))
                                    @php $sc = $statusColors[$assessment['heightStatus']] ?? $statusColors['unknown']; @endphp
                                    <span class="inline-block mt-1 px-2 py-0.5 text-[10px] font-medium rounded-full {{ $sc['bg'] }} {{ $sc['border'] }} {{ $sc['text'] }} border">
                                        {{ $sc['label'] }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-2xl p-5">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-mintGreen-100 to-mintGreen-50 dark:from-mintGreen-950/30 dark:to-mintGreen-950/30 flex items-center justify-center text-2xl">
                                🧠
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Lingkar Kepala') }}</p>
                                <p class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ $latestGrowth->head_circumference_label ?? '—' }}</p>
                                @if ($assessment && isset($assessment['headStatus']))
                                    @php $sc = $statusColors[$assessment['headStatus']] ?? $statusColors['unknown']; @endphp
                                    <span class="inline-block mt-1 px-2 py-0.5 text-[10px] font-medium rounded-full {{ $sc['bg'] }} {{ $sc['border'] }} {{ $sc['text'] }} border">
                                        {{ $sc['label'] }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Growth Chart -->
            @if ($growthHistory->count() >= 2)
                <div class="mb-8 bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl">
                    <div class="p-4 sm:p-6">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-100 mb-4">📈 {{ __('Grafik Pertumbuhan') }}</h3>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mb-3">{{ __('Garis putus-putus menunjukkan standar pertumbuhan WHO') }}</p>
                        <x-growth-chart
                            :growths="$growthHistory"
                            :who-weight="$whoWeight"
                            :who-height="$whoHeight"
                            :who-head="$whoHead"
                            :child-gender="$child->gender"
                        />
                    </div>
                </div>
            @endif

            @if ($growths->isEmpty() && !$request->hasAny(['date_from', 'date_to']))
                <!-- Empty State -->
                <div class="text-center py-16">
                    <div class="w-24 h-24 mx-auto mb-6 rounded-3xl bg-gradient-to-br from-softPink-50 to-skyBlue-50 dark:from-softPink-950/30 dark:to-skyBlue-950/30 flex items-center justify-center">
                        <span class="text-5xl">📏</span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-2">{{ __('Belum Ada Data Pertumbuhan') }}</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-6 max-w-md mx-auto">{{ __('Mulai catat pertumbuhan ' . ($child->nickname ?? $child->name) . ' untuk memantau perkembangannya dari waktu ke waktu.') }}</p>
                    <a href="{{ route('growth.create', $child) }}" class="btn-primary min-h-[44px]">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        {{ __('Tambah Pengukuran Pertama') }}
                    </a>
                </div>
            @else
                <!-- Date Filter -->
                <div class="mb-6" x-data="{ showFilters: false }">
                    <div class="flex items-center gap-2 mb-3">
                        <button type="button" @click="showFilters = !showFilters" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-xl text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition min-h-[44px]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            {{ __('Filter Periode') }}
                        </button>
                        @if (request()->hasAny(['date_from', 'date_to']))
                            <span class="text-sm text-gray-400 dark:text-gray-500">
                                {{ $growths->total() }} {{ __('hasil') }}
                            </span>
                            <a href="{{ route('growth.index', $child) }}" class="text-sm text-red-500 hover:text-red-600 dark:text-red-400 dark:hover:text-red-300 underline">
                                {{ __('Reset') }}
                            </a>
                        @endif
                    </div>

                    <div x-show="showFilters" x-transition x-cloak class="p-4 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-soft">
                        <form action="{{ route('growth.index', $child) }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                            <div class="flex-1">
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('Dari Tanggal') }}</label>
                                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-softPink-300 dark:focus:ring-softPink-600 focus:border-softPink-400 dark:focus:border-softPink-500 min-h-[44px]">
                            </div>
                            <div class="flex-1">
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('Sampai Tanggal') }}</label>
                                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-softPink-300 dark:focus:ring-softPink-600 focus:border-softPink-400 dark:focus:border-softPink-500 min-h-[44px]">
                            </div>
                            <div class="flex items-end gap-2">
                                <button type="submit" class="px-5 py-2.5 bg-softPink-500 hover:bg-softPink-600 text-white font-medium rounded-xl text-sm transition min-h-[44px]">
                                    {{ __('Terapkan') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Growth History Table -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl">
                    <div class="p-4 sm:p-6">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-100 mb-4">📋 {{ __('Riwayat Pengukuran') }}</h3>
                        <div class="overflow-x-auto -mx-4 sm:mx-0 px-4 sm:px-0">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-100 dark:border-gray-700">
                                        <th class="text-left py-3 px-4 font-medium text-gray-500 dark:text-gray-400">{{ __('Tanggal') }}</th>
                                        <th class="text-left py-3 px-4 font-medium text-gray-500 dark:text-gray-400">{{ __('Berat') }}</th>
                                        <th class="text-left py-3 px-4 font-medium text-gray-500 dark:text-gray-400">{{ __('Tinggi') }}</th>
                                        <th class="text-left py-3 px-4 font-medium text-gray-500 dark:text-gray-400">{{ __('Lingkar Kepala') }}</th>
                                        <th class="text-left py-3 px-4 font-medium text-gray-500 dark:text-gray-400">{{ __('Catatan') }}</th>
                                        <th class="text-right py-3 px-4 font-medium text-gray-500 dark:text-gray-400">{{ __('Aksi') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($growths as $growth)
                                        <tr class="border-b border-gray-50 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                            <td class="py-3 px-4 text-gray-800 dark:text-gray-100 font-medium">{{ $growth->formatted_date }}</td>
                                            <td class="py-3 px-4 text-gray-600 dark:text-gray-300">{{ $growth->weight_label ?? '—' }}</td>
                                            <td class="py-3 px-4 text-gray-600 dark:text-gray-300">{{ $growth->height_label ?? '—' }}</td>
                                            <td class="py-3 px-4 text-gray-600 dark:text-gray-300">{{ $growth->head_circumference_label ?? '—' }}</td>
                                            <td class="py-3 px-4 text-gray-500 dark:text-gray-400 max-w-[200px] truncate">{{ $growth->notes ?? '—' }}</td>
                                            <td class="py-3 px-4 text-right">
                                                <div class="flex items-center justify-end gap-2">
                                                    <a href="{{ route('growth.edit', [$child, $growth]) }}" class="text-skyBlue-600 hover:text-skyBlue-700 dark:text-skyBlue-400 dark:hover:text-skyBlue-300 text-xs font-medium">
                                                        ✏️ {{ __('Edit') }}
                                                    </a>
                                                    <button type="button" class="text-red-500 hover:text-red-600 dark:text-red-400 dark:hover:text-red-300 text-xs font-medium"
                                                        x-data
                                                        x-on:click.prevent="$dispatch('delete-confirm', 'delete-growth-{{ $growth->id }}')">
                                                        🗑️ {{ __('Hapus') }}
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Delete Confirmation Modals --}}
                        @foreach ($growths as $growth)
                            <x-confirm-delete
                                id="delete-growth-{{ $growth->id }}"
                                title="{{ __('Hapus Data Pengukuran') }}"
                                message="{{ __('Apakah Anda yakin ingin menghapus data pengukuran ini? Tindakan ini tidak dapat dibatalkan.') }}"
                                action="{{ route('growth.destroy', [$child, $growth]) }}"
                            />
                        @endforeach

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $growths->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
