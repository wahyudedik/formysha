<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <x-breadcrumb :items="[
                    ['url' => route('facility.dashboard'), 'label' => __('Dashboard')],
                    ['url' => route('facility.referrals.index'), 'label' => __('Rujukan')],
                    ['label' => __('Detail Rujukan')],
                ]" />
                <h2 class="mt-2 font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                    🔄 {{ __('Detail Rujukan') }}
                </h2>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @if ($referral->status->value === 'pending' && $referral->to_tenant_id === $tenant->id)
                    <form method="POST" action="{{ route('facility.referrals.accept', $referral) }}" class="inline" x-data="{ loading: false }" @submit="loading = true">
                        @csrf
                        <button type="submit" x-bind:disabled="loading" class="inline-flex items-center justify-center gap-1 px-4 py-2.5 bg-mintGreen-500 hover:bg-mintGreen-600 text-white font-medium rounded-xl text-sm transition shadow-soft min-h-[44px] disabled:opacity-50">
                            ✅ {{ __('Terima') }}
                        </button>
                    </form>
                @endif

                @if (in_array($referral->status->value, ['pending', 'accepted']))
                    <form method="POST" action="{{ route('facility.referrals.complete', $referral) }}" class="inline" x-data="{ loading: false }" @submit="loading = true">
                        @csrf
                        <button type="submit" x-bind:disabled="loading" class="inline-flex items-center justify-center gap-1 px-4 py-2.5 bg-skyBlue-500 hover:bg-skyBlue-600 text-white font-medium rounded-xl text-sm transition shadow-soft min-h-[44px] disabled:opacity-50">
                            ✓ {{ __('Selesai') }}
                        </button>
                    </form>
                @endif

                @if (in_array($referral->status->value, ['pending', 'accepted']))
                    <button type="button" @click="$dispatch('delete-confirm', { id: 'cancel-referral-{{ $referral->id }}' })" class="inline-flex items-center justify-center gap-1 px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white font-medium rounded-xl text-sm transition shadow-soft min-h-[44px]">
                        ✕ {{ __('Batalkan') }}
                    </button>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-6">
                @include('facility-admin.partials.sidebar')
                <div class="flex-1 min-w-0">
                    @if (session('success'))
                        <div class="mb-6 p-4 bg-mintGreen-50 dark:bg-mintGreen-950/30 border border-mintGreen-200 dark:border-mintGreen-800 text-mintGreen-700 dark:text-mintGreen-400 rounded-xl" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Status & Date -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl p-4 sm:p-6 mb-6">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 dark:bg-yellow-950/30 text-yellow-700 dark:text-yellow-400',
                                        'accepted' => 'bg-skyBlue-100 dark:bg-skyBlue-950/30 text-skyBlue-700 dark:text-skyBlue-400',
                                        'completed' => 'bg-mintGreen-100 dark:bg-mintGreen-950/30 text-mintGreen-700 dark:text-mintGreen-400',
                                        'cancelled' => 'bg-red-100 dark:bg-red-950/30 text-red-700 dark:text-red-400',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-medium {{ $statusColors[$referral->status->value] ?? '' }}">
                                    {{ $referral->status->label() }}
                                </span>
                            </div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $referral->created_at->format('d M Y, H:i') }}</span>
                        </div>
                    </div>

                    <!-- Patient Info -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl p-4 sm:p-6 mb-6">
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">{{ __('Pasien') }}</h4>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-softPink-100 dark:bg-softPink-950/30 flex items-center justify-center text-lg">
                                👶
                            </div>
                            <div>
                                <p class="font-medium text-gray-800 dark:text-gray-100">{{ $referral->child->name ?? '-' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $referral->child->date_of_birth?->format('d M Y') ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Facility Flow -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl p-4 sm:p-6">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">{{ __('Fasilitas Asal') }}</h4>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-lavender-100 dark:bg-lavender-950/30 flex items-center justify-center text-lg">
                                    🏥
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800 dark:text-gray-100">{{ $referral->fromTenant->name ?? '-' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl p-4 sm:p-6">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">{{ __('Fasilitas Tujuan') }}</h4>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-warmYellow-100 dark:bg-warmYellow-950/30 flex items-center justify-center text-lg">
                                    🏥
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800 dark:text-gray-100">{{ $referral->toTenant->name ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Referring Staff -->
                    @if ($referral->referringStaff)
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl p-4 sm:p-6 mb-6">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">{{ __('Staf Perujuk') }}</h4>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-skyBlue-100 dark:bg-skyBlue-950/30 flex items-center justify-center text-lg">
                                    👨‍⚕️
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800 dark:text-gray-100">{{ $referral->referringStaff->name ?? '-' }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $referral->referringStaff->email ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Reason -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl p-4 sm:p-6 mb-6">
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">{{ __('Alasan Rujukan') }}</h4>
                        <div class="text-gray-800 dark:text-gray-200 whitespace-pre-wrap leading-relaxed">{{ $referral->reason }}</div>
                    </div>

                    <!-- Clinical Summary -->
                    @if ($referral->clinical_summary)
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl p-4 sm:p-6 mb-6">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">{{ __('Ringkasan Klinis') }}</h4>
                            <div class="text-gray-800 dark:text-gray-200 whitespace-pre-wrap leading-relaxed">{{ $referral->clinical_summary }}</div>
                        </div>
                    @endif

                    <!-- Notes -->
                    @if ($referral->notes)
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl p-4 sm:p-6 mb-6">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">{{ __('Catatan Tambahan') }}</h4>
                            <div class="text-gray-800 dark:text-gray-200 whitespace-pre-wrap leading-relaxed">{{ $referral->notes }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if (in_array($referral->status->value, ['pending', 'accepted']))
        <x-confirm-delete
            id="cancel-referral-{{ $referral->id }}"
            :title="__('Batalkan Rujukan')"
            :message="__('Yakin ingin membatalkan rujukan ini?')"
            :action="route('facility.referrals.cancel', $referral)"
            method="POST"
        />
    @endif
</x-app-layout>
