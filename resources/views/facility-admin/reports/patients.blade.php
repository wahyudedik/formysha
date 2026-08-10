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
                    👶 {{ __('Laporan Pasien') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Summary -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl p-4 sm:p-6">
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $summary['total'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Total Pasien') }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl p-4 sm:p-6">
                    <p class="text-2xl font-bold text-mintGreen-600 dark:text-mintGreen-400">{{ $summary['active'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Pasien Aktif') }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl p-4 sm:p-6">
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $summary['revoked'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Pasien Dicabut') }}</p>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl">
                @if ($patients->isEmpty())
                    <div class="text-center py-12">
                        <div class="text-4xl mb-3">👶</div>
                        <p class="text-gray-500 dark:text-gray-400">{{ __('Belum ada pasien terdaftar.') }}</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Pasien') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Orang Tua') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Status') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Tanggal Hubung') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($patients as $patient)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-softPink-100 dark:bg-softPink-950/30 flex items-center justify-center text-sm">
                                                    👶
                                                </div>
                                                <span class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $patient->child->name ?? '-' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                            {{ $patient->parentUser->name ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $statusColor = $patient->status === 'active'
                                                    ? 'bg-mintGreen-100 dark:bg-mintGreen-950/30 text-mintGreen-700 dark:text-mintGreen-400'
                                                    : 'bg-red-100 dark:bg-red-950/30 text-red-700 dark:text-red-400';
                                            @endphp
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium {{ $statusColor }}">
                                                {{ $patient->status === 'active' ? __('Aktif') : __('Dicabut') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $patient->linked_at?->format('d M Y') ?? $patient->created_at->format('d M Y') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                        {{ $patients->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
