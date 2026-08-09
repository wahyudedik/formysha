<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                📜 {{ __('Riwayat Langganan') }}
            </h2>
            <a href="{{ route('subscription.current') }}" class="btn-secondary text-sm">
                ← {{ __('Kembali') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto sm:px-6 lg:px-8">
        <x-breadcrumb :items="[
            ['label' => 'Langganan', 'url' => route('subscription.current')],
            ['label' => 'Riwayat'],
        ]" />

        {{-- Desktop Table --}}
        <div class="hidden md:block bg-white dark:bg-gray-800 rounded-2xl shadow-soft overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-700">
                            <th class="text-left px-6 py-4 font-semibold text-gray-600 dark:text-gray-300">Tanggal</th>
                            <th class="text-left px-6 py-4 font-semibold text-gray-600 dark:text-gray-300">Paket</th>
                            <th class="text-left px-6 py-4 font-semibold text-gray-600 dark:text-gray-300">Status</th>
                            <th class="text-left px-6 py-4 font-semibold text-gray-600 dark:text-gray-300">Periode</th>
                            <th class="text-right px-6 py-4 font-semibold text-gray-600 dark:text-gray-300">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($subscriptions as $sub)
                            <tr class="border-b border-gray-50 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-750 transition">
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-300 text-xs">
                                    {{ $sub->created_at->locale('id')->isoFormat('D MMMM YYYY') }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-medium text-gray-800 dark:text-gray-100">{{ $sub->plan->name ?? '-' }}</span>
                                    @if ($sub->plan)
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $sub->plan->getPriceMonthlyFormatted() }}/bulan</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium
                                        {{ match($sub->status) {
                                            'active' => 'bg-mintGreen-100 text-mintGreen-600 dark:bg-mintGreen-950/30 dark:text-mintGreen-400',
                                            'pending' => 'bg-warmYellow-100 text-warmYellow-600 dark:bg-warmYellow-950/30 dark:text-warmYellow-400',
                                            'inactive' => 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400',
                                            'cancelled' => 'bg-red-100 text-red-600 dark:bg-red-950/30 dark:text-red-400',
                                            'past_due' => 'bg-orange-100 text-orange-600 dark:bg-orange-950/30 dark:text-orange-400',
                                            default => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
                                        } }}">
                                        {{ match($sub->status) {
                                            'active' => '✅ Aktif',
                                            'pending' => '⏳ Pending',
                                            'inactive' => '⏸️ Tidak Aktif',
                                            'cancelled' => '❌ Dibatalkan',
                                            'past_due' => '⚠️ Terlambat',
                                            default => ucfirst($sub->status),
                                        } }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-500 dark:text-gray-400">
                                    @if ($sub->starts_at && $sub->ends_at)
                                        {{ $sub->starts_at->locale('id')->isoFormat('D MMM') }} — {{ $sub->ends_at->locale('id')->isoFormat('D MMM YYYY') }}
                                    @elseif ($sub->starts_at)
                                        {{ $sub->starts_at->locale('id')->isoFormat('D MMM YYYY') }} — ...
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if ($sub->status === 'pending')
                                        <a href="{{ route('subscription.payment.upload', $sub) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-warmYellow-50 text-warmYellow-600 hover:bg-warmYellow-100 dark:bg-warmYellow-950/30 dark:text-warmYellow-400 dark:hover:bg-warmYellow-950/50 transition">
                                            💳 Bayar
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12">
                                    <x-empty-state icon="📜" title="Belum Ada Riwayat" description="Riwayat langganan Anda akan muncul di sini." />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Mobile Cards --}}
        <div class="md:hidden space-y-3">
            @forelse ($subscriptions as $sub)
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-4 border border-gray-100 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-semibold text-gray-800 dark:text-gray-100">{{ $sub->plan->name ?? '-' }}</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium
                            {{ match($sub->status) {
                                'active' => 'bg-mintGreen-100 text-mintGreen-600 dark:bg-mintGreen-950/30 dark:text-mintGreen-400',
                                'pending' => 'bg-warmYellow-100 text-warmYellow-600 dark:bg-warmYellow-950/30 dark:text-warmYellow-400',
                                'inactive' => 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400',
                                'cancelled' => 'bg-red-100 text-red-600 dark:bg-red-950/30 dark:text-red-400',
                                default => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
                            } }}">
                            {{ ucfirst($sub->status) }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $sub->created_at->locale('id')->isoFormat('D MMMM YYYY') }}</p>
                    @if ($sub->starts_at && $sub->ends_at)
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $sub->starts_at->locale('id')->isoFormat('D MMM') }} — {{ $sub->ends_at->locale('id')->isoFormat('D MMM YYYY') }}</p>
                    @endif
                    @if ($sub->status === 'pending')
                        <a href="{{ route('subscription.payment.upload', $sub) }}" class="mt-3 block text-center py-2 rounded-xl bg-warmYellow-50 text-warmYellow-600 text-xs font-medium hover:bg-warmYellow-100 dark:bg-warmYellow-950/30 dark:text-warmYellow-400 dark:hover:bg-warmYellow-950/50 transition">
                            💳 Bayar Sekarang
                        </a>
                    @endif
                </div>
            @empty
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-6">
                    <x-empty-state icon="📜" title="Belum Ada Riwayat" description="Riwayat langganan Anda akan muncul di sini." />
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if ($subscriptions->hasPages())
            <div class="mt-6">
                {{ $subscriptions->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
