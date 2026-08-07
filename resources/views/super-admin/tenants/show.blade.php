<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                🏢 {{ $tenant->name }}
            </h2>
            <div class="flex items-center gap-2">
                <form method="POST" action="{{ route('super-admin.tenants.toggle-status', $tenant) }}" class="inline">
                    @csrf
                    <button type="submit" class="btn-secondary text-sm {{ $tenant->is_active ? 'hover:bg-warmYellow-50 hover:border-warmYellow-300' : 'hover:bg-mintGreen-50 hover:border-mintGreen-300' }}">
                        {{ $tenant->is_active ? '⏸️ Nonaktifkan' : '▶️ Aktifkan' }}
                    </button>
                </form>
                <a href="{{ route('super-admin.tenants.edit', $tenant) }}" class="btn-accent text-sm">
                    ✏️ Edit
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-6">
            @include('super-admin.partials.sidebar')

            <div class="flex-1 min-w-0">
                <x-breadcrumb :items="[
                    ['label' => 'Dashboard', 'url' => route('super-admin.dashboard')],
                    ['label' => 'Tenants', 'url' => route('super-admin.tenants.index')],
                    ['label' => $tenant->name],
                ]" />

                {{-- Tenant Info --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white rounded-2xl shadow-soft p-5 border border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-skyBlue-50 flex items-center justify-center">
                                <span class="text-2xl">👤</span>
                            </div>
                            <div>
                                <p class="text-lg font-bold text-gray-800">{{ $tenant->users_count ?? 0 }}</p>
                                <p class="text-xs text-gray-500">Pengguna</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-soft p-5 border border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-softPink-50 flex items-center justify-center">
                                <span class="text-2xl">👶</span>
                            </div>
                            <div>
                                <p class="text-lg font-bold text-gray-800">{{ $tenant->children_count ?? 0 }}</p>
                                <p class="text-xs text-gray-500">Anak</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-soft p-5 border border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-lavender-50 flex items-center justify-center">
                                <span class="text-2xl">📋</span>
                            </div>
                            <div>
                                <p class="text-lg font-bold text-gray-800">{{ $tenant->subscriptions_count ?? 0 }}</p>
                                <p class="text-xs text-gray-500">Langganan</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-soft p-5 border border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl {{ $tenant->is_active ? 'bg-mintGreen-50' : 'bg-red-50' }} flex items-center justify-center">
                                <span class="text-2xl">{{ $tenant->is_active ? '✅' : '⛔' }}</span>
                            </div>
                            <div>
                                <p class="text-sm font-bold {{ $tenant->is_active ? 'text-mintGreen-600' : 'text-red-600' }}">{{ $tenant->is_active ? 'Aktif' : 'Nonaktif' }}</p>
                                <p class="text-xs text-gray-500">Status</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Detail Info --}}
                    <div class="bg-white rounded-2xl shadow-soft overflow-hidden">
                        <div class="p-6 border-b border-gray-100">
                            <h3 class="font-semibold text-gray-800">📋 {{ __('Detail Tenant') }}</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500">Nama</span>
                                <span class="text-sm font-medium text-gray-800">{{ $tenant->name }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500">Slug</span>
                                <span class="text-sm font-medium text-gray-800 font-mono">{{ $tenant->slug }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500">Domain</span>
                                <span class="text-sm font-medium text-gray-800">{{ $tenant->domain ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500">Status</span>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium {{ $tenant->is_active ? 'bg-mintGreen-100 text-mintGreen-600' : 'bg-red-100 text-red-600' }}">
                                    {{ $tenant->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500">Dibuat</span>
                                <span class="text-sm font-medium text-gray-800">{{ $tenant->created_at->locale('id')->isoFormat('D MMMM YYYY, HH:mm') }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500">Terakhir Diperbarui</span>
                                <span class="text-sm font-medium text-gray-800">{{ $tenant->updated_at->locale('id')->isoFormat('D MMMM YYYY, HH:mm') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Subscription History --}}
                    <div class="bg-white rounded-2xl shadow-soft overflow-hidden">
                        <div class="p-6 border-b border-gray-100">
                            <h3 class="font-semibold text-gray-800">💳 {{ __('Riwayat Langganan') }}</h3>
                        </div>
                        <div class="p-6">
                            @php
                                $subscriptions = $tenant->subscriptions()->with('plan')->latest()->take(5)->get();
                            @endphp
                            @if ($subscriptions->isEmpty())
                                <div class="text-center py-8">
                                    <div class="text-4xl mb-3">💳</div>
                                    <p class="text-sm text-gray-500">Belum ada riwayat langganan.</p>
                                </div>
                            @else
                                <div class="space-y-3">
                                    @foreach ($subscriptions as $sub)
                                        <div class="p-3 rounded-xl bg-gray-50">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <p class="text-sm font-medium text-gray-800">{{ $sub->plan->name ?? '-' }}</p>
                                                    <p class="text-xs text-gray-500">{{ $sub->created_at->locale('id')->isoFormat('D MMM YYYY') }}</p>
                                                </div>
                                                <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium
                                                    {{ match($sub->status) {
                                                        'active' => 'bg-mintGreen-100 text-mintGreen-600',
                                                        'pending' => 'bg-warmYellow-100 text-warmYellow-600',
                                                        'cancelled' => 'bg-red-100 text-red-600',
                                                        'past_due' => 'bg-orange-100 text-orange-600',
                                                        default => 'bg-gray-100 text-gray-600',
                                                    } }}">
                                                    {{ ucfirst($sub->status) }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Delete --}}
                <div class="mt-6 bg-white rounded-2xl shadow-soft p-6">
                    <h3 class="font-semibold text-red-600 mb-2">⚠️ Zona Bahaya</h3>
                    <p class="text-sm text-gray-500 mb-4">Menghapus tenant akan menghapus semua data terkait secara permanen.</p>
                    <button
                        type="button"
                        class="inline-flex items-center px-4 py-2 bg-red-500 text-white text-sm font-semibold rounded-xl hover:bg-red-600 transition"
                        x-data
                        x-on:click="$dispatch('delete-confirm', 'delete-tenant')"
                    >
                        🗑️ Hapus Tenant
                    </button>
                </div>

                <x-confirm-delete
                    id="delete-tenant"
                    title="Hapus Tenant"
                    message="Apakah Anda yakin ingin menghapus tenant ini? Semua data terkait akan dihapus secara permanen."
                    action="{{ route('super-admin.tenants.destroy', $tenant) }}"
                    method="DELETE"
                />
            </div>
        </div>
    </div>
</x-app-layout>
