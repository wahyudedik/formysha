<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('facility.reports.index') }}" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                    📋 {{ __('Laporan Catatan Klinis') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Filter -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl p-4 sm:p-6 mb-6">
                <form method="GET" action="{{ route('facility.reports.clinical-notes') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    <div class="flex-1">
                        <x-input-label for="from" :value="__('Dari')" />
                        <x-text-input id="from" name="from" type="date" class="mt-1 block w-full" :value="$from" />
                    </div>
                    <div class="flex-1">
                        <x-input-label for="to" :value="__('Sampai')" />
                        <x-text-input id="to" name="to" type="date" class="mt-1 block w-full" :value="$to" />
                    </div>
                    <div class="pt-5">
                        <button type="submit" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-skyBlue-500 hover:bg-skyBlue-600 text-white font-medium rounded-xl text-sm transition shadow-soft min-h-[44px]">
                            🔍 {{ __('Filter') }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Summary -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl p-4 sm:p-6">
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $summary['total'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Total Catatan') }}</p>
                </div>
                @php
                    $typeLabels = [
                        'consultation' => 'Konsultasi',
                        'examination' => 'Pemeriksaan',
                        'treatment' => 'Penanganan',
                        'follow-up' => 'Tindak Lanjut',
                    ];
                @endphp
                @foreach ($summary['by_type'] as $type => $count)
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl p-4 sm:p-6">
                        <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $count }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $typeLabels[$type] ?? $type }}</p>
                    </div>
                @endforeach
            </div>

            <!-- Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl">
                @if ($notes->isEmpty())
                    <div class="text-center py-12">
                        <div class="text-4xl mb-3">📋</div>
                        <p class="text-gray-500 dark:text-gray-400">{{ __('Tidak ada catatan klinis pada periode ini.') }}</p>
                    </div>
                @else
                    <div class="overflow-x-auto -mx-4 sm:mx-0 px-4 sm:px-0">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Tanggal') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Pasien') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Judul') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Tipe') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Staf') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($notes as $note)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $note->created_at->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-gray-100">
                                            {{ $note->child->name ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300 max-w-xs truncate">
                                            {{ $note->title }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium bg-lavender-100 dark:bg-lavender-950/30 text-lavender-700 dark:text-lavender-400">
                                                {{ $note->type->label() }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                            {{ $note->staffUser->name ?? '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                        {{ $notes->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
