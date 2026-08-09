<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                🏢 {{ __('Manajemen Tenant') }}
            </h2>
            <a href="{{ route('super-admin.tenants.create') }}" class="btn-primary text-sm min-h-[44px] inline-flex items-center self-start">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('Tambah Tenant') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-6">
            @include('super-admin.partials.sidebar')

            <div class="flex-1 min-w-0">
                <x-breadcrumb :items="[
                    ['label' => 'Dashboard', 'url' => route('super-admin.dashboard')],
                    ['label' => 'Tenants'],
                ]" />

                {{-- Desktop Table --}}
                <div class="hidden md:block bg-white dark:bg-gray-800 rounded-2xl shadow-soft overflow-hidden">
                    <div class="overflow-x-auto -mx-4 sm:mx-0 px-4 sm:px-0">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-100 dark:border-gray-700">
                                    <th class="text-left px-6 py-4 font-semibold text-gray-600 dark:text-gray-400">Nama</th>
                                    <th class="text-left px-6 py-4 font-semibold text-gray-600 dark:text-gray-400">Pemilik</th>
                                    <th class="text-left px-6 py-4 font-semibold text-gray-600 dark:text-gray-400">Status</th>
                                    <th class="text-left px-6 py-4 font-semibold text-gray-600 dark:text-gray-400">Anak</th>
                                    <th class="text-left px-6 py-4 font-semibold text-gray-600 dark:text-gray-400">Dibuat</th>
                                    <th class="text-right px-6 py-4 font-semibold text-gray-600 dark:text-gray-400">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tenants as $tenant)
                                    <tr class="border-b border-gray-50 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-750 transition">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-skyBlue-400 to-lavender-400 flex items-center justify-center text-white text-xs font-bold shrink-0">
                                                    {{ strtoupper(substr($tenant->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <a href="{{ route('super-admin.tenants.show', $tenant) }}" class="font-medium text-gray-800 dark:text-gray-100 hover:text-skyBlue-600 dark:hover:text-skyBlue-400 transition">
                                                        {{ $tenant->name }}
                                                    </a>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $tenant->slug }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-gray-600 dark:text-gray-300">
                                            {{ $tenant->users->first()?->name ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium {{ $tenant->is_active ? 'bg-mintGreen-100 text-mintGreen-600 dark:bg-mintGreen-950/30 dark:text-mintGreen-400' : 'bg-red-100 text-red-600 dark:bg-red-950/30 dark:text-red-400' }}">
                                                {{ $tenant->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-gray-600 dark:text-gray-300">{{ $tenant->children_count ?? 0 }}</td>
                                        <td class="px-6 py-4 text-gray-500 dark:text-gray-400 text-xs">{{ $tenant->created_at->locale('id')->isoFormat('D MMM YYYY') }}</td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <a href="{{ route('super-admin.tenants.show', $tenant) }}" class="p-2 rounded-lg text-gray-400 dark:text-gray-500 hover:text-skyBlue-600 dark:hover:text-skyBlue-400 hover:bg-skyBlue-50 dark:hover:bg-skyBlue-950/20 transition" title="Lihat">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </a>
                                                <form method="POST" action="{{ route('super-admin.tenants.toggle-status', $tenant) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="p-2 rounded-lg {{ $tenant->is_active ? 'text-gray-400 dark:text-gray-500 hover:text-warmYellow-600 dark:hover:text-warmYellow-400 hover:bg-warmYellow-50 dark:hover:bg-warmYellow-950/20' : 'text-gray-400 dark:text-gray-500 hover:text-mintGreen-600 dark:hover:text-mintGreen-400 hover:bg-mintGreen-50 dark:hover:bg-mintGreen-950/20' }} transition" title="{{ $tenant->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            @if ($tenant->is_active)
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                            @else
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            @endif
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12">
                                            <x-empty-state icon="🏢" title="Belum Ada Tenant" description="Buat tenant pertama untuk memulai." action-url="{{ route('super-admin.tenants.create') }}" action-text="Tambah Tenant" />
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Mobile Cards --}}
                <div class="md:hidden space-y-3">
                    @forelse ($tenants as $tenant)
                        <a href="{{ route('super-admin.tenants.show', $tenant) }}" class="block bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-4 border border-gray-100 dark:border-gray-700 min-h-[44px]">
                            <div class="flex items-center gap-3">
                                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-skyBlue-400 to-lavender-400 flex items-center justify-center text-white text-sm font-bold shrink-0">
                                    {{ strtoupper(substr($tenant->name, 0, 1)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-gray-800 dark:text-gray-100 truncate">{{ $tenant->name }}</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $tenant->slug }} · {{ $tenant->children_count ?? 0 }} anak</p>
                                </div>
                                <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium {{ $tenant->is_active ? 'bg-mintGreen-100 text-mintGreen-600 dark:bg-mintGreen-950/30 dark:text-mintGreen-400' : 'bg-red-100 text-red-600 dark:bg-red-950/30 dark:text-red-400' }}">
                                    {{ $tenant->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>
                        </a>
                    @empty
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-6">
                            <x-empty-state icon="🏢" title="Belum Ada Tenant" description="Buat tenant pertama untuk memulai." action-url="{{ route('super-admin.tenants.create') }}" action-text="Tambah Tenant" />
                        </div>
                    @endforelse
                </div>

                {{-- Pagination --}}
                @if ($tenants->hasPages())
                    <div class="mt-6">
                        {{ $tenants->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
