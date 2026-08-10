<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <x-breadcrumb :items="[
                    ['url' => route('facility.dashboard'), 'label' => __('Dashboard')],
                    ['label' => __('Laporan')],
                ]" />
                <h2 class="mt-2 font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                    📊 {{ __('Laporan Fasilitas') }}
                </h2>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('facility.reports.clinical-notes') }}" class="inline-flex items-center justify-center gap-1 px-4 py-2.5 bg-skyBlue-500 hover:bg-skyBlue-600 text-white font-medium rounded-xl text-sm transition shadow-soft min-h-[44px]">
                    📋 {{ __('Catatan Klinis') }}
                </a>
                <a href="{{ route('facility.reports.patients') }}" class="inline-flex items-center justify-center gap-1 px-4 py-2.5 bg-mintGreen-500 hover:bg-mintGreen-600 text-white font-medium rounded-xl text-sm transition shadow-soft min-h-[44px]">
                    👶 {{ __('Pasien') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-6">
                @include('facility-admin.partials.sidebar')
                <div class="flex-1 min-w-0">
                    <!-- Stats Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                        <!-- Total Staff -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl p-4 sm:p-6">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-2xl bg-skyBlue-100 dark:bg-skyBlue-950/30 flex items-center justify-center text-2xl">
                                    👨‍⚕️
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $stats['total_staff'] }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Total Staf') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Total Patients -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl p-4 sm:p-6">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-2xl bg-softPink-100 dark:bg-softPink-950/30 flex items-center justify-center text-2xl">
                                    👶
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $stats['total_patients'] }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Total Pasien') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Total Notes -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl p-4 sm:p-6">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-2xl bg-lavender-100 dark:bg-lavender-950/30 flex items-center justify-center text-2xl">
                                    📋
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $stats['total_notes'] }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Total Catatan') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Pending Referrals -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl p-4 sm:p-6">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-2xl bg-warmYellow-100 dark:bg-warmYellow-950/30 flex items-center justify-center text-2xl">
                                    🔄
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $stats['pending_referrals'] }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Rujukan Pending') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Stats -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Staff & Patients -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl p-4 sm:p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Ringkasan Staf & Pasien') }}</h3>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Staf Aktif') }}</span>
                                    <span class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $stats['active_staff'] }} / {{ $stats['total_staff'] }}</span>
                                </div>
                                <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Pasien Aktif') }}</span>
                                    <span class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $stats['active_patients'] }} / {{ $stats['total_patients'] }}</span>
                                </div>
                                <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Catatan Bulan Ini') }}</span>
                                    <span class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $stats['notes_this_month'] }}</span>
                                </div>
                                <div class="flex items-center justify-between py-2">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Rujukan Keluar') }}</span>
                                    <span class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $stats['total_referrals_outgoing'] }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Notes by Type -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl p-4 sm:p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Catatan Berdasarkan Tipe') }}</h3>
                            @if ($notesByType->isEmpty())
                                <x-empty-state
                                    icon="📋"
                                    :title="__('Belum Ada Data')"
                                    :description="__('Data catatan klinis belum tersedia.')"
                                />
                            @else
                                <div class="space-y-3">
                                    @php
                                        $typeLabels = [
                                            'consultation' => 'Konsultasi',
                                            'examination' => 'Pemeriksaan',
                                            'treatment' => 'Penanganan',
                                            'follow-up' => 'Tindak Lanjut',
                                        ];
                                        $maxCount = $notesByType->max();
                                    @endphp
                                    @foreach ($notesByType as $type => $count)
                                        @php
                                            $percentage = $maxCount > 0 ? ($count / $maxCount) * 100 : 0;
                                        @endphp
                                        <div>
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $typeLabels[$type] ?? $type }}</span>
                                                <span class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $count }}</span>
                                            </div>
                                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                                <div class="bg-lavender-500 h-2 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
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
    </div>
</x-app-layout>
