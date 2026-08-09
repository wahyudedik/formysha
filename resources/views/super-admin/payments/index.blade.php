<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            💳 {{ __('Manajemen Pembayaran') }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-6">
            @include('super-admin.partials.sidebar')

            <div class="flex-1 min-w-0">
                <x-breadcrumb :items="[
                    ['label' => 'Dashboard', 'url' => route('super-admin.dashboard')],
                    ['label' => 'Pembayaran'],
                ]" />

                {{-- Status Filter --}}
                <div class="flex flex-wrap gap-2 mb-4 min-h-[44px]">
                    <a href="{{ route('super-admin.payments.index') }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-medium transition {{ !request('status') ? 'bg-skyBlue-100 text-skyBlue-600 dark:bg-skyBlue-950/30 dark:text-skyBlue-400' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600' }}">
                        Semua
                    </a>
                    <a href="{{ route('super-admin.payments.index', ['status' => 'pending']) }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-medium transition {{ request('status') === 'pending' ? 'bg-warmYellow-100 text-warmYellow-600 dark:bg-warmYellow-950/30 dark:text-warmYellow-400' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600' }}">
                        ⏳ Pending
                    </a>
                    <a href="{{ route('super-admin.payments.index', ['status' => 'approved']) }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-medium transition {{ request('status') === 'approved' ? 'bg-mintGreen-100 text-mintGreen-600 dark:bg-mintGreen-950/30 dark:text-mintGreen-400' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600' }}">
                        ✅ Disetujui
                    </a>
                    <a href="{{ route('super-admin.payments.index', ['status' => 'rejected']) }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-medium transition {{ request('status') === 'rejected' ? 'bg-red-100 text-red-600 dark:bg-red-950/30 dark:text-red-400' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600' }}">
                        ❌ Ditolak
                    </a>
                </div>

                {{-- Desktop Table --}}
                <div class="hidden md:block bg-white dark:bg-gray-800 rounded-2xl shadow-soft overflow-hidden">
                    <div class="overflow-x-auto -mx-4 sm:mx-0 px-4 sm:px-0">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-100 dark:border-gray-700">
                                    <th class="text-left px-6 py-4 font-semibold text-gray-600 dark:text-gray-400">Tenant</th>
                                    <th class="text-left px-6 py-4 font-semibold text-gray-600 dark:text-gray-400">Jumlah</th>
                                    <th class="text-left px-6 py-4 font-semibold text-gray-600 dark:text-gray-400">Bank</th>
                                    <th class="text-left px-6 py-4 font-semibold text-gray-600 dark:text-gray-400">Status</th>
                                    <th class="text-left px-6 py-4 font-semibold text-gray-600 dark:text-gray-400">Tanggal</th>
                                    <th class="text-right px-6 py-4 font-semibold text-gray-600 dark:text-gray-400">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($payments as $payment)
                                    <tr class="border-b border-gray-50 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-750 transition">
                                        <td class="px-6 py-4">
                                            <div>
                                                <p class="font-medium text-gray-800 dark:text-gray-100">{{ $payment->tenant->name ?? '-' }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $payment->subscription->plan->name ?? '-' }}</p>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 font-semibold text-gray-800 dark:text-gray-100">{{ $payment->getAmountFormatted() }}</td>
                                        <td class="px-6 py-4 text-gray-600 dark:text-gray-300">{{ $payment->bank_name ?? '-' }}</td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium
                                                {{ match($payment->status) {
                                                    'pending' => 'bg-warmYellow-100 text-warmYellow-600 dark:bg-warmYellow-950/30 dark:text-warmYellow-400',
                                                    'approved' => 'bg-mintGreen-100 text-mintGreen-600 dark:bg-mintGreen-950/30 dark:text-mintGreen-400',
                                                    'rejected' => 'bg-red-100 text-red-600 dark:bg-red-950/30 dark:text-red-400',
                                                    default => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300',
                                                } }}">
                                                {{ match($payment->status) {
                                                    'pending' => '⏳ Pending',
                                                    'approved' => '✅ Disetujui',
                                                    'rejected' => '❌ Ditolak',
                                                    default => ucfirst($payment->status),
                                                } }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-gray-500 dark:text-gray-400 text-xs">{{ $payment->created_at->locale('id')->isoFormat('D MMM YYYY') }}</td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('super-admin.payments.show', $payment) }}" class="inline-flex items-center min-h-[44px] px-3 py-1.5 rounded-lg text-xs font-medium bg-skyBlue-50 text-skyBlue-600 hover:bg-skyBlue-100 dark:bg-skyBlue-950/30 dark:text-skyBlue-400 dark:hover:bg-skyBlue-950/50 transition">
                                                Lihat Detail
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12">
                                            <x-empty-state icon="💳" title="Belum Ada Pembayaran" description="Belum ada transaksi pembayaran." />
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Mobile Cards --}}
                <div class="md:hidden space-y-3">
                    @forelse ($payments as $payment)
                        <a href="{{ route('super-admin.payments.show', $payment) }}" class="block bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-4 border border-gray-100 dark:border-gray-700 min-h-[44px]">
                            <div class="flex items-center justify-between mb-2">
                                <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium
                                    {{ match($payment->status) {
                                        'pending' => 'bg-warmYellow-100 text-warmYellow-600 dark:bg-warmYellow-950/30 dark:text-warmYellow-400',
                                        'approved' => 'bg-mintGreen-100 text-mintGreen-600 dark:bg-mintGreen-950/30 dark:text-mintGreen-400',
                                        'rejected' => 'bg-red-100 text-red-600 dark:bg-red-950/30 dark:text-red-400',
                                        default => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300',
                                    } }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                                <span class="text-sm font-bold text-gray-800 dark:text-gray-100">{{ $payment->getAmountFormatted() }}</span>
                            </div>
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $payment->tenant->name ?? '-' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $payment->bank_name ?? '-' }} · {{ $payment->created_at->locale('id')->diffForHumans() }}</p>
                        </a>
                    @empty
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-6">
                            <x-empty-state icon="💳" title="Belum Ada Pembayaran" description="Belum ada transaksi pembayaran." />
                        </div>
                    @endforelse
                </div>

                {{-- Pagination --}}
                @if ($payments->hasPages())
                    <div class="mt-6">
                        {{ $payments->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
