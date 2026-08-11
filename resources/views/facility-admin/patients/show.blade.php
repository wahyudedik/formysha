<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <x-breadcrumb :items="[
                    ['url' => route('facility.dashboard'), 'label' => __('Dashboard')],
                    ['url' => route('facility.patients.index'), 'label' => __('Pasien')],
                    ['label' => __('Detail Pasien')],
                ]" />
                <h2 class="mt-2 font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                    👶 {{ __('Detail Pasien') }}
                </h2>
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

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl p-4 sm:p-6 lg:p-8">
                        <!-- Profile Header -->
                        <div class="flex items-center gap-4 mb-6 pb-6 border-b border-gray-100 dark:border-gray-700">
                            <div class="w-16 h-16 rounded-2xl bg-softPink-100 dark:bg-softPink-950/30 flex items-center justify-center text-2xl">
                                👶
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ $patientLink->child->name ?? '-' }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $patientLink->child->date_of_birth?->format('d M Y') ?? '-' }}</p>
                            </div>
                        </div>

                        <!-- Link Code -->
                        <div class="mb-6 p-4 bg-softPink-50 dark:bg-softPink-950/30 rounded-2xl border border-softPink-100 dark:border-softPink-900/30">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div>
                                    <p class="text-sm font-medium text-softPink-700 dark:text-softPink-300">{{ __('Kode Tautan') }}</p>
                                    <p class="text-2xl font-bold text-softPink-800 dark:text-softPink-200 font-mono tracking-wider">{{ $patientLink->link_code }}</p>
                                </div>
                                <button type="button" onclick="const v='{{ $patientLink->link_code }}'; if(navigator.clipboard){navigator.clipboard.writeText(v)}else{const t=document.createElement('textarea');t.value=v;t.style.position='fixed';t.style.left='-9999px';document.body.appendChild(t);t.select();document.execCommand('copy');document.body.removeChild(t)}" class="inline-flex items-center justify-center gap-1 px-4 py-2 bg-softPink-500 hover:bg-softPink-600 text-white font-medium rounded-xl text-sm transition min-h-[44px]">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                    {{ __('Salin Kode') }}
                                </button>
                            </div>
                            <p class="mt-2 text-xs text-softPink-600 dark:text-softPink-400">
                                {{ __('Berikan kode ini kepada orang tua untuk menghubungkan akun mereka.') }}
                            </p>
                        </div>

                        <!-- Details -->
                        <div class="space-y-4">
                            <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400 sm:w-40">{{ __('Orang Tua') }}</span>
                                <span class="text-sm text-gray-800 dark:text-gray-200">{{ $patientLink->parentUser->name ?? '-' }}</span>
                            </div>

                            <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400 sm:w-40">{{ __('Email Orang Tua') }}</span>
                                <span class="text-sm text-gray-800 dark:text-gray-200">{{ $patientLink->parentUser->email ?? '-' }}</span>
                            </div>

                            <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400 sm:w-40">{{ __('Status') }}</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium {{ $patientLink->status->value === 'active' ? 'bg-mintGreen-100 dark:bg-mintGreen-950/30 text-mintGreen-700 dark:text-mintGreen-400' : ($patientLink->status->value === 'pending' ? 'bg-warmYellow-100 dark:bg-warmYellow-950/30 text-warmYellow-700 dark:text-warmYellow-400' : 'bg-red-100 dark:bg-red-950/30 text-red-700 dark:text-red-400') }}">
                                    {{ $patientLink->status->label() }}
                                </span>
                            </div>

                            <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400 sm:w-40">{{ __('Izin Akses') }}</span>
                                <div class="flex flex-wrap gap-1">
                                    @if ($patientLink->permissions)
                                        @foreach ($patientLink->permissions as $perm)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-medium bg-lavender-100 dark:bg-lavender-950/30 text-lavender-700 dark:text-lavender-400">
                                                {{ str_replace('_', ' ', ucfirst($perm)) }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-sm text-gray-400">-</span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400 sm:w-40">{{ __('Dibuat') }}</span>
                                <span class="text-sm text-gray-800 dark:text-gray-200">{{ $patientLink->created_at->format('d M Y H:i') }}</span>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="mt-8 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                            @if ($patientLink->status->value !== 'revoked')
                                <button type="button" @click="$dispatch('delete-confirm', { id: 'revoke-patient-{{ $patientLink->id }}' })" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-xl shadow-soft transition-all duration-200 min-h-[44px]">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                    </svg>
                                    {{ __('Cabut Tautan') }}
                                </button>
                            @endif
                            <button type="button" @click="$dispatch('delete-confirm', { id: 'delete-patient-{{ $patientLink->id }}' })" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-xl transition min-h-[44px]">
                                {{ __('Hapus') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($patientLink->status->value !== 'revoked')
        <x-confirm-delete
            id="revoke-patient-{{ $patientLink->id }}"
            :title="__('Cabut Tautan')"
            :message="__('Yakin ingin mencabut tautan ini? Orang tua tidak akan bisa mengakses data pasien.')"
            :action="route('facility.patients.revoke', $patientLink)"
            method="POST"
        />
    @endif

    <x-confirm-delete
        id="delete-patient-{{ $patientLink->id }}"
        :title="__('Hapus Tautan')"
        :message="__('Yakin ingin menghapus tautan ini? Data akan dihapus permanen.')"
        :action="route('facility.patients.destroy', $patientLink)"
        method="DELETE"
    />
</x-app-layout>
