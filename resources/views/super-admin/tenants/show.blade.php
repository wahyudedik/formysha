<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                🏢 {{ $tenant->name }}
            </h2>
            <div class="flex flex-row flex-wrap gap-2">
                <form method="POST" action="{{ route('super-admin.tenants.toggle-status', $tenant) }}" class="inline">
                    @csrf
                    <button type="submit" class="btn-secondary text-sm min-h-[44px] {{ $tenant->is_active ? 'hover:bg-warmYellow-50 hover:border-warmYellow-300 dark:hover:bg-warmYellow-950/20 dark:hover:border-warmYellow-600' : 'hover:bg-mintGreen-50 hover:border-mintGreen-300 dark:hover:bg-mintGreen-950/20 dark:hover:border-mintGreen-600' }}">
                        {{ $tenant->is_active ? '⏸️ Nonaktifkan' : '▶️ Aktifkan' }}
                    </button>
                </form>
                <a href="{{ route('super-admin.tenants.edit', $tenant) }}" class="btn-accent text-sm min-h-[44px] inline-flex items-center">
                    ✏️ Edit
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-5 border border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-skyBlue-50 dark:bg-skyBlue-950/30 flex items-center justify-center">
                                <span class="text-2xl">👤</span>
                            </div>
                            <div>
                                <p class="text-lg font-bold text-gray-800 dark:text-gray-100">{{ $tenant->users_count ?? 0 }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Pengguna</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-5 border border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-softPink-50 dark:bg-softPink-950/30 flex items-center justify-center">
                                <span class="text-2xl">👶</span>
                            </div>
                            <div>
                                <p class="text-lg font-bold text-gray-800 dark:text-gray-100">{{ $tenant->children_count ?? 0 }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Anak</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-5 border border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-lavender-50 dark:bg-lavender-950/30 flex items-center justify-center">
                                <span class="text-2xl">📋</span>
                            </div>
                            <div>
                                <p class="text-lg font-bold text-gray-800 dark:text-gray-100">{{ $tenant->subscriptions_count ?? 0 }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Langganan</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-5 border border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl {{ $tenant->is_active ? 'bg-mintGreen-50 dark:bg-mintGreen-950/30' : 'bg-red-50 dark:bg-red-950/30' }} flex items-center justify-center">
                                <span class="text-2xl">{{ $tenant->is_active ? '✅' : '⛔' }}</span>
                            </div>
                            <div>
                                <p class="text-sm font-bold {{ $tenant->is_active ? 'text-mintGreen-600 dark:text-mintGreen-400' : 'text-red-600 dark:text-red-400' }}">{{ $tenant->is_active ? 'Aktif' : 'Nonaktif' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Status</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- B2B Facility Details --}}
                @if ($tenant->isB2B() && $b2bData)
                    <div class="mb-6">
                        <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">🏥 {{ __('Detail Fasilitas B2B') }}</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-5 border border-gray-100 dark:border-gray-700">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-xl bg-skyBlue-50 dark:bg-skyBlue-950/30 flex items-center justify-center">
                                        <span class="text-2xl">👨‍⚕️</span>
                                    </div>
                                    <div>
                                        <p class="text-lg font-bold text-gray-800 dark:text-gray-100">{{ $b2bData['staff_count'] }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Total Staf</p>
                                    </div>
                                </div>
                                <div class="mt-3 text-xs text-mintGreen-600 dark:text-mintGreen-400">{{ $b2bData['active_staff_count'] }} aktif</div>
                            </div>

                            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-5 border border-gray-100 dark:border-gray-700">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-xl bg-softPink-50 dark:bg-softPink-950/30 flex items-center justify-center">
                                        <span class="text-2xl">👶</span>
                                    </div>
                                    <div>
                                        <p class="text-lg font-bold text-gray-800 dark:text-gray-100">{{ $b2bData['patient_link_count'] }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Tautan Pasien</p>
                                    </div>
                                </div>
                                <div class="mt-3 text-xs text-mintGreen-600 dark:text-mintGreen-400">{{ $b2bData['active_patient_count'] }} aktif</div>
                            </div>

                            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-5 border border-gray-100 dark:border-gray-700">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-xl bg-lavender-50 dark:bg-lavender-950/30 flex items-center justify-center">
                                        <span class="text-2xl">📋</span>
                                    </div>
                                    <div>
                                        <p class="text-lg font-bold text-gray-800 dark:text-gray-100">{{ $b2bData['clinical_note_count'] }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Catatan Klinis</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-5 border border-gray-100 dark:border-gray-700">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-xl bg-warmYellow-50 dark:bg-warmYellow-950/30 flex items-center justify-center">
                                        <span class="text-2xl">🔄</span>
                                    </div>
                                    <div>
                                        <p class="text-lg font-bold text-gray-800 dark:text-gray-100">{{ $b2bData['referral_count'] }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Total Rujukan</p>
                                    </div>
                                </div>
                                <div class="mt-3 text-xs {{ $b2bData['pending_referral_count'] > 0 ? 'text-warmYellow-600 dark:text-warmYellow-400' : 'text-gray-500 dark:text-gray-400' }}">{{ $b2bData['pending_referral_count'] }} pending</div>
                            </div>
                        </div>
                    </div>

                    {{-- Facility Info --}}
                    @if ($tenant->facility)
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft overflow-hidden mb-6">
                            <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-gray-700">
                                <h3 class="font-semibold text-gray-800 dark:text-gray-100">🏥 {{ __('Informasi Fasilitas') }}</h3>
                            </div>
                            <div class="p-4 sm:p-6 space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Tipe Fasilitas</span>
                                    <span class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $tenant->getTypeLabel() }}</span>
                                </div>
                                @if ($tenant->address)
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Alamat</span>
                                        <span class="text-sm font-medium text-gray-800 dark:text-gray-100 text-right max-w-[60%]">{{ $tenant->address }}</span>
                                    </div>
                                @endif
                                @if ($tenant->phone)
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Telepon</span>
                                        <span class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $tenant->phone }}</span>
                                    </div>
                                @endif
                                @if ($tenant->email_institution)
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Email</span>
                                        <span class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $tenant->email_institution }}</span>
                                    </div>
                                @endif
                                @if ($tenant->license_number)
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">No. Lisensi</span>
                                        <span class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $tenant->license_number }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Staff List --}}
                    @if ($b2bData['staff']->isNotEmpty())
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft overflow-hidden mb-6">
                            <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-gray-700">
                                <h3 class="font-semibold text-gray-800 dark:text-gray-100">👨‍⚕️ {{ __('Daftar Staf') }}</h3>
                            </div>
                            <div class="p-4 sm:p-6">
                                <div class="overflow-x-auto -mx-4 sm:mx-0 px-4 sm:px-0">
                                    <table class="w-full text-sm">
                                        <thead>
                                            <tr class="text-left text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-gray-700">
                                                <th class="pb-3 font-medium">Nama</th>
                                                <th class="pb-3 font-medium">Role</th>
                                                <th class="pb-3 font-medium">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                                            @foreach ($b2bData['staff'] as $s)
                                                <tr>
                                                    <td class="py-3 text-gray-800 dark:text-gray-100">{{ $s->user?->name ?? '-' }}</td>
                                                    <td class="py-3 text-gray-600 dark:text-gray-300">{{ $s->role ?? '-' }}</td>
                                                    <td class="py-3">
                                                        <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium {{ $s->is_active ? 'bg-mintGreen-100 text-mintGreen-600 dark:bg-mintGreen-950/30 dark:text-mintGreen-400' : 'bg-red-100 text-red-600 dark:bg-red-950/30 dark:text-red-400' }}">
                                                            {{ $s->is_active ? 'Aktif' : 'Nonaktif' }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Detail Info --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft overflow-hidden">
                        <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="font-semibold text-gray-800 dark:text-gray-100">📋 {{ __('Detail Tenant') }}</h3>
                        </div>
                        <div class="p-4 sm:p-6 space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Nama</span>
                                <span class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $tenant->name }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Slug</span>
                                <span class="text-sm font-medium text-gray-800 dark:text-gray-100 font-mono">{{ $tenant->slug }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Domain</span>
                                <span class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $tenant->domain ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Status</span>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium {{ $tenant->is_active ? 'bg-mintGreen-100 text-mintGreen-600 dark:bg-mintGreen-950/30 dark:text-mintGreen-400' : 'bg-red-100 text-red-600 dark:bg-red-950/30 dark:text-red-400' }}">
                                    {{ $tenant->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Dibuat</span>
                                <span class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $tenant->created_at->locale('id')->isoFormat('D MMMM YYYY, HH:mm') }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Terakhir Diperbarui</span>
                                <span class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $tenant->updated_at->locale('id')->isoFormat('D MMMM YYYY, HH:mm') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Subscription History --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft overflow-hidden">
                        <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="font-semibold text-gray-800 dark:text-gray-100">💳 {{ __('Riwayat Langganan') }}</h3>
                        </div>
                        <div class="p-4 sm:p-6">
                            @php
                                $subscriptions = $tenant->subscriptions()->with('plan')->latest()->take(5)->get();
                            @endphp
                            @if ($subscriptions->isEmpty())
                                <div class="text-center py-8">
                                    <div class="text-4xl mb-3">💳</div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada riwayat langganan.</p>
                                </div>
                            @else
                                <div class="space-y-3">
                                    @foreach ($subscriptions as $sub)
                                        <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $sub->plan->name ?? '-' }}</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $sub->created_at->locale('id')->isoFormat('D MMM YYYY') }}</p>
                                                </div>
                                                <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium
                                                    {{ match($sub->status) {
                                                        'active' => 'bg-mintGreen-100 text-mintGreen-600 dark:bg-mintGreen-950/30 dark:text-mintGreen-400',
                                                        'pending' => 'bg-warmYellow-100 text-warmYellow-600 dark:bg-warmYellow-950/30 dark:text-warmYellow-400',
                                                        'cancelled' => 'bg-red-100 text-red-600 dark:bg-red-950/30 dark:text-red-400',
                                                        'past_due' => 'bg-orange-100 text-orange-600 dark:bg-orange-950/30 dark:text-orange-400',
                                                        default => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300',
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
                <div class="mt-6 bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-4 sm:p-6">
                    <h3 class="font-semibold text-red-600 dark:text-red-400 mb-2">⚠️ Zona Bahaya</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Menghapus tenant akan menghapus semua data terkait secara permanen.</p>
                    <button
                        type="button"
                        class="inline-flex items-center px-4 py-2 bg-red-500 text-white text-sm font-semibold rounded-xl hover:bg-red-600 transition min-h-[44px]"
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
