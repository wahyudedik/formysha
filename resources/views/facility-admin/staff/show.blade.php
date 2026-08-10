<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <x-breadcrumb :items="[
                    ['url' => route('facility.dashboard'), 'label' => __('Dashboard')],
                    ['url' => route('facility.staff.index'), 'label' => __('Staf')],
                    ['label' => __('Detail Staf')],
                ]" />
                <h2 class="mt-2 font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                    👨‍⚕️ {{ __('Detail Staf') }}
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
                            <div class="w-16 h-16 rounded-2xl bg-skyBlue-100 dark:bg-skyBlue-950/30 flex items-center justify-center text-2xl">
                                {{ $staff->staff_role->value === 'doctor' ? '👨‍⚕️' : ($staff->staff_role->value === 'midwife' ? '👩‍⚕️' : '🧑‍⚕️') }}
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ $staff->user->name ?? '-' }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $staff->user->email ?? '-' }}</p>
                            </div>
                        </div>

                        <!-- Details -->
                        <div class="space-y-4">
                            <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400 sm:w-40">{{ __('Role') }}</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium bg-skyBlue-100 dark:bg-skyBlue-950/30 text-skyBlue-700 dark:text-skyBlue-400">
                                    {{ $staff->staff_role->label() }}
                                </span>
                            </div>

                            <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400 sm:w-40">{{ __('Spesialisasi') }}</span>
                                <span class="text-sm text-gray-800 dark:text-gray-200">{{ $staff->specialization ?? '-' }}</span>
                            </div>

                            <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400 sm:w-40">{{ __('No. STR/SIP') }}</span>
                                <span class="text-sm text-gray-800 dark:text-gray-200">{{ $staff->license_number ?? '-' }}</span>
                            </div>

                            <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400 sm:w-40">{{ __('Status') }}</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium {{ $staff->is_active ? 'bg-mintGreen-100 dark:bg-mintGreen-950/30 text-mintGreen-700 dark:text-mintGreen-400' : 'bg-red-100 dark:bg-red-950/30 text-red-700 dark:text-red-400' }}">
                                    {{ $staff->is_active ? __('Aktif') : __('Nonaktif') }}
                                </span>
                            </div>

                            <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400 sm:w-40">{{ __('Tanggal Bergabung') }}</span>
                                <span class="text-sm text-gray-800 dark:text-gray-200">{{ $staff->created_at->format('d M Y') }}</span>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="mt-8 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                            <a href="{{ route('facility.staff.edit', $staff) }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-skyBlue-500 hover:bg-skyBlue-600 text-white font-semibold rounded-xl shadow-soft transition-all duration-200 min-h-[44px]">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                {{ __('Edit Staf') }}
                            </a>
                            <button type="button" @click="$dispatch('delete-confirm', { id: 'delete-staff-{{ $staff->id }}' })" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-xl shadow-soft transition-all duration-200 min-h-[44px]">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                </svg>
                                {{ __('Nonaktifkan') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-confirm-delete
        id="delete-staff-{{ $staff->id }}"
        :title="__('Nonaktifkan Staf')"
        :message="__('Yakin ingin menonaktifkan staf ini? Staf tidak akan bisa mengakses sistem.')"
        :action="route('facility.staff.destroy', $staff)"
        method="DELETE"
    />
</x-app-layout>
