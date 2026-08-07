<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                📋 {{ __('Manajemen Paket') }}
            </h2>
            <a href="{{ route('super-admin.plans.create') }}" class="btn-primary text-sm">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('Tambah Paket') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-6">
            @include('super-admin.partials.sidebar')

            <div class="flex-1 min-w-0">
                <x-breadcrumb :items="[
                    ['label' => 'Dashboard', 'url' => route('super-admin.dashboard')],
                    ['label' => 'Paket'],
                ]" />

                {{-- Desktop Table --}}
                <div class="hidden md:block bg-white rounded-2xl shadow-soft overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-100">
                                    <th class="text-left px-6 py-4 font-semibold text-gray-600">Nama</th>
                                    <th class="text-left px-6 py-4 font-semibold text-gray-600">Harga/Bulan</th>
                                    <th class="text-left px-6 py-4 font-semibold text-gray-600">Harga/Tahun</th>
                                    <th class="text-left px-6 py-4 font-semibold text-gray-600">Batas Anak</th>
                                    <th class="text-left px-6 py-4 font-semibold text-gray-600">Storage</th>
                                    <th class="text-left px-6 py-4 font-semibold text-gray-600">Status</th>
                                    <th class="text-right px-6 py-4 font-semibold text-gray-600">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($plans as $plan)
                                    <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                                        <td class="px-6 py-4">
                                            <div>
                                                <p class="font-medium text-gray-800">{{ $plan->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $plan->slug }}</p>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 font-semibold text-gray-800">{{ $plan->getPriceMonthlyFormatted() }}</td>
                                        <td class="px-6 py-4 text-gray-600">{{ $plan->getPriceYearlyFormatted() }}</td>
                                        <td class="px-6 py-4 text-gray-600">{{ $plan->max_children === -1 ? '∞' : $plan->max_children }}</td>
                                        <td class="px-6 py-4 text-gray-600">{{ $plan->getStorageFormatted() }}</td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium {{ $plan->is_active ? 'bg-mintGreen-100 text-mintGreen-600' : 'bg-gray-100 text-gray-500' }}">
                                                {{ $plan->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <a href="{{ route('super-admin.plans.edit', $plan) }}" class="p-2 rounded-lg text-gray-400 hover:text-skyBlue-600 hover:bg-skyBlue-50 transition" title="Edit">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                                <form method="POST" action="{{ route('super-admin.plans.destroy', $plan) }}" class="inline" x-data>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-2 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition" title="Hapus" x-on:click="return confirm('Yakin ingin menghapus paket ini?')">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
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
                        <div class="bg-white rounded-2xl shadow-soft p-4 border border-gray-100">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="font-semibold text-gray-800">{{ $plan->name }}</h3>
                                <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium {{ $plan->is_active ? 'bg-mintGreen-100 text-mintGreen-600' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $plan->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-xs text-gray-500 mb-3">
                                <span>Bulan: <strong class="text-gray-800">{{ $plan->getPriceMonthlyFormatted() }}</strong></span>
                                <span>Tahun: <strong class="text-gray-800">{{ $plan->getPriceYearlyFormatted() }}</strong></span>
                                <span>Anak: <strong class="text-gray-800">{{ $plan->max_children === -1 ? '∞' : $plan->max_children }}</strong></span>
                                <span>Storage: <strong class="text-gray-800">{{ $plan->getStorageFormatted() }}</strong></span>
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ route('super-admin.plans.edit', $plan) }}" class="flex-1 text-center py-2 rounded-xl bg-skyBlue-50 text-skyBlue-600 text-xs font-medium hover:bg-skyBlue-100 transition">
                                    ✏️ Edit
                                </a>
                                <form method="POST" action="{{ route('super-admin.plans.destroy', $plan) }}" class="flex-1" x-data>
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full py-2 rounded-xl bg-red-50 text-red-600 text-xs font-medium hover:bg-red-100 transition" x-on:click="return confirm('Yakin ingin menghapus paket ini?')">
                                        🗑️ Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white rounded-2xl shadow-soft p-6">
                            <x-empty-state icon="📋" title="Belum Ada Paket" description="Buat paket langganan pertama." action-url="{{ route('super-admin.plans.create') }}" action-text="Tambah Paket" />
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
