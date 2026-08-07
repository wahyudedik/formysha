<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            📜 {{ __('Audit Log') }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-6">
            @include('super-admin.partials.sidebar')

            <div class="flex-1 min-w-0">
                <x-breadcrumb :items="[
                    ['label' => 'Dashboard', 'url' => route('super-admin.dashboard')],
                    ['label' => 'Audit Log'],
                ]" />

                {{-- Desktop Table --}}
                <div class="hidden md:block bg-white rounded-2xl shadow-soft overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-100">
                                    <th class="text-left px-6 py-4 font-semibold text-gray-600">Waktu</th>
                                    <th class="text-left px-6 py-4 font-semibold text-gray-600">Pengguna</th>
                                    <th class="text-left px-6 py-4 font-semibold text-gray-600">Aksi</th>
                                    <th class="text-left px-6 py-4 font-semibold text-gray-600">Subjek</th>
                                    <th class="text-left px-6 py-4 font-semibold text-gray-600">Detail</th>
                                    <th class="text-left px-6 py-4 font-semibold text-gray-600">IP</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($logs as $log)
                                    <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 text-xs text-gray-500 whitespace-nowrap">
                                            {{ $log->created_at->locale('id')->isoFormat('D MMM YYYY, HH:mm:ss') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="text-sm font-medium text-gray-800">{{ $log->user->name ?? 'System' }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium
                                                {{ match($log->event) {
                                                    'created' => 'bg-mintGreen-100 text-mintGreen-600',
                                                    'updated' => 'bg-skyBlue-100 text-skyBlue-600',
                                                    'deleted' => 'bg-red-100 text-red-600',
                                                    default => 'bg-gray-100 text-gray-600',
                                                } }}">
                                                {{ ucfirst($log->event) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="text-sm text-gray-600">{{ $log->auditable_type ? class_basename($log->auditable_type) : '-' }}</span>
                                            @if ($log->auditable_id)
                                                <span class="text-xs text-gray-400 ml-1">#{{ $log->auditable_id }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if ($log->new_values && count($log->new_values) > 0)
                                                <div class="text-xs text-gray-500 max-w-xs truncate">
                                                    @foreach (array_slice($log->new_values, 0, 3) as $key => $value)
                                                        <span class="font-mono">{{ $key }}</span>@if (!$loop->last), @endif
                                                    @endforeach
                                                    @if (count($log->new_values) > 3)
                                                        <span class="text-gray-400">+{{ count($log->new_values) - 3 }} lagi</span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-xs text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-xs text-gray-400 font-mono">
                                            {{ $log->ip_address ?? '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12">
                                            <x-empty-state icon="📜" title="Belum Ada Audit Log" description="Audit log akan muncul setelah ada aktivitas." />
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Mobile Cards --}}
                <div class="md:hidden space-y-3">
                    @forelse ($logs as $log)
                        <div class="bg-white rounded-2xl shadow-soft p-4 border border-gray-100">
                            <div class="flex items-center justify-between mb-2">
                                <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium
                                    {{ match($log->event) {
                                        'created' => 'bg-mintGreen-100 text-mintGreen-600',
                                        'updated' => 'bg-skyBlue-100 text-skyBlue-600',
                                        'deleted' => 'bg-red-100 text-red-600',
                                        default => 'bg-gray-100 text-gray-600',
                                    } }}">
                                    {{ ucfirst($log->event) }}
                                </span>
                                <span class="text-xs text-gray-400">{{ $log->created_at->locale('id')->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm font-medium text-gray-800">{{ $log->user->name ?? 'System' }}</p>
                            <p class="text-xs text-gray-500">
                                {{ $log->auditable_type ? class_basename($log->auditable_type) : '-' }}
                                @if ($log->ip_address)
                                    · <span class="font-mono">{{ $log->ip_address }}</span>
                                @endif
                            </p>
                        </div>
                    @empty
                        <div class="bg-white rounded-2xl shadow-soft p-6">
                            <x-empty-state icon="📜" title="Belum Ada Audit Log" description="Audit log akan muncul setelah ada aktivitas." />
                        </div>
                    @endforelse
                </div>

                {{-- Pagination --}}
                @if ($logs->hasPages())
                    <div class="mt-6">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
