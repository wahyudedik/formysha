<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                📋 {{ __('Manajemen Paket') }}
            </h2>
            <a href="{{ route('super-admin.plans.create') }}" class="btn-primary text-sm min-h-[44px] inline-flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('Tambah Paket') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-6">
            @include('super-admin.partials.sidebar')

            <div class="flex-1 min-w-0">
                <x-breadcrumb :items="[
                    ['label' => 'Dashboard', 'url' => route('super-admin.dashboard')],
                    ['label' => 'Paket'],
                ]" />

                {{-- Desktop Table --}}
                <div class="hidden md:block bg-white dark:bg-gray-800 rounded-2xl shadow-soft overflow-hidden">
                    <div class="overflow-x-auto -mx-4 sm:mx-0 px-4 sm:px-0">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-100 dark:border-gray-700">
                                    <th class="text-left px-6 py-4 font-semibold text-gray-600 dark:text-gray-400">Nama</th>
                                    <th class="text-left px-6 py-4 font-semibold text-gray-600 dark:text-gray-400">Harga/Bulan</th>
                                    <th class="text-left px-6 py-4 font-semibold text-gray-600 dark:text-gray-400">Harga/Tahun</th>
                                    <th class="text-left px-6 py-4 font-semibold text-gray-600 dark:text-gray-400">Batas Anak</th>
                                    <th class="text-left px-6 py-4 font-semibold text-gray-600 dark:text-gray-400">Storage</th>
                                    <th class="text-left px-6 py-4 font-semibold text-gray-600 dark:text-gray-400">Status</th>
                                    <th class="text-right px-6 py-4 font-semibold text-gray-600 dark:text-gray-400">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($plans as $plan)
                                    <tr class="border-b border-gray-50 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-750 transition">
                                        <td class="px-6 py-4">
                                            <div>
                                                <p class="font-medium text-gray-800 dark:text-gray-100">{{ $plan->name }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $plan->slug }}</p>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 font-semibold text-gray-800 dark:text-gray-100">{{ $plan->getPriceMonthlyFormatted() }}</td>
                                        <td class="px-6 py-4 text-gray-600 dark:text-gray-300">{{ $plan->getPriceYearlyFormatted() }}</td>
                                        <td class="px-6 py-4 text-gray-600 dark:text-gray-300">{{ $plan->max_children === -1 ? '∞' : $plan->max_children }}</td>
                                        <td class="px-6 py-4 text-gray-600 dark:text-gray-300">{{ $plan->getStorageFormatted() }}</td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium {{ $plan->is_active ? 'bg-mintGreen-100 text-mintGreen-600 dark:bg-mintGreen-950/30 dark:text-mintGreen-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">
                                                {{ $plan->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <a href="{{ route('super-admin.plans.edit', $plan) }}" class="p-2 rounded-lg text-gray-400 dark:text-gray-500 hover:text-skyBlue-600 dark:hover:text-skyBlue-400 hover:bg-skyBlue-50 dark:hover:bg-skyBlue-950/20 transition" title="Edit">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                                <button type="button" class="p-2 rounded-lg text-gray-400 dark:text-gray-500 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/20 transition" title="Hapus"
                                                    x-data
                                                    x-on:click.prevent="$dispatch('delete-confirm', 'delete-plan-{{ $plan->id }}')">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-12">
                                            <x-empty-state icon="📋" title="Belum Ada Paket" description="Buat paket langganan pertama." action-url="{{ route('super-admin.plans.create') }}" action-text="Tambah Paket" />
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Mobile Cards --}}
                <div class="md:hidden space-y-3">
                    @forelse ($plans as $plan)
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-4 border border-gray-100 dark:border-gray-700 min-h-[44px]">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="font-semibold text-gray-800 dark:text-gray-100">{{ $plan->name }}</h3>
                                <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium {{ $plan->is_active ? 'bg-mintGreen-100 text-mintGreen-600 dark:bg-mintGreen-950/30 dark:text-mintGreen-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">
                                    {{ $plan->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-xs text-gray-500 dark:text-gray-400 mb-3">
                                <span>Bulan: <strong class="text-gray-800 dark:text-gray-100">{{ $plan->getPriceMonthlyFormatted() }}</strong></span>
                                <span>Tahun: <strong class="text-gray-800 dark:text-gray-100">{{ $plan->getPriceYearlyFormatted() }}</strong></span>
                                <span>Anak: <strong class="text-gray-800 dark:text-gray-100">{{ $plan->max_children === -1 ? '∞' : $plan->max_children }}</strong></span>
                                <span>Storage: <strong class="text-gray-800 dark:text-gray-100">{{ $plan->getStorageFormatted() }}</strong></span>
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ route('super-admin.plans.edit', $plan) }}" class="flex-1 min-h-[44px] flex items-center justify-center py-2 rounded-xl bg-skyBlue-50 dark:bg-skyBlue-950/30 text-skyBlue-600 dark:text-skyBlue-400 text-xs font-medium hover:bg-skyBlue-100 dark:hover:bg-skyBlue-950/50 transition">
                                    ✏️ Edit
                                </a>
                                <button type="button"
                                    class="w-full min-h-[44px] flex items-center justify-center py-2 rounded-xl bg-red-50 dark:bg-red-950/30 text-red-600 dark:text-red-400 text-xs font-medium hover:bg-red-100 dark:hover:bg-red-950/50 transition"
                                    x-data
                                    x-on:click.prevent="$dispatch('delete-confirm', 'delete-plan-{{ $plan->id }}')">
                                    🗑️ Hapus
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-6">
                            <x-empty-state icon="📋" title="Belum Ada Paket" description="Buat paket langganan pertama." action-url="{{ route('super-admin.plans.create') }}" action-text="Tambah Paket" />
                        </div>
                    @endforelse
                </div>

                {{-- Delete Confirmation Modals --}}
                @foreach ($plans as $plan)
                    <x-confirm-delete
                        id="delete-plan-{{ $plan->id }}"
                        title="{{ __('Hapus Paket') }}"
                        message="{{ __('Apakah Anda yakin ingin menghapus paket') }} '{{ $plan->name }}'? {{ __('Tindakan ini tidak dapat dibatalkan.') }}"
                        action="{{ route('super-admin.plans.destroy', $plan) }}"
                    />
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
