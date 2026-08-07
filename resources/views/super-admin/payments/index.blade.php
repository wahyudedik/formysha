<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            💳 {{ __('Manajemen Pembayaran') }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-6">
            @include('super-admin.partials.sidebar')

            <div class="flex-1 min-w-0">
                <x-breadcrumb :items="[
                    ['label' => 'Dashboard', 'url' => route('super-admin.dashboard')],
                    ['label' => 'Pembayaran'],
                ]" />

                {{-- Status Filter --}}
                <div class="flex flex-wrap gap-2 mb-4">
                    <a href="{{ route('super-admin.payments.index') }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-medium transition {{ !request('status') ? 'bg-skyBlue-100 text-skyBlue-600' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Semua
                    </a>
                    <a href="{{ route('super-admin.payments.index', ['status' => 'pending']) }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-medium transition {{ request('status') === 'pending' ? 'bg-warmYellow-100 text-warmYellow-600' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        ⏳ Pending
                    </a>
                    <a href="{{ route('super-admin.payments.index', ['status' => 'approved']) }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-medium transition {{ request('status') === 'approved' ? 'bg-mintGreen-100 text-mintGreen-600' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        ✅ Disetujui
                    </a>
                    <a href="{{ route('super-admin.payments.index', ['status' => 'rejected']) }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-medium transition {{ request('status') === 'rejected' ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        ❌ Ditolak
                    </a>
                </div>

                {{-- Desktop Table --}}
                <div class="hidden md:block bg-white rounded-2xl shadow-soft overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-100">
                                    <th class="text-left px-6 py-4 font-semibold text-gray-600">Tenant</th>
                                    <th class="text-left px-6 py-4 font-semibold text-gray-600">Jumlah</th>
                                    <th class="text-left px-6 py-4 font-semibold text-gray-600">Bank</th>
                                    <th class="text-left px-6 py-4 font-semibold text-gray-600">Status</th>
                                    <th class="text-left px-6 py-4 font-semibold text-gray-600">Tanggal</th>
                                    <th class="text-right px-6 py-4 font-semibold text-gray-600">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($payments as $payment)
                                    <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                                        <td class="px-6 py-4">
                                            <div>
                                                <p class="font-medium text-gray-800">{{ $payment->tenant->name ?? '-' }}</p>
                                                <p class="text-xs text-gray-500">{{ $payment->subscription->plan->name ?? '-' }}</p>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 font-semibold text-gray-800">{{ $payment->getAmountFormatted() }}</td>
                                        <td class="px-6 py-4 text-gray-600">{{ $payment->bank_name ?? '-' }}</td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium
                                                {{ match($payment->status) {
                                                    'pending' => 'bg-warmYellow-100 text-warmYellow-600',
                                                    'approved' => 'bg-mintGreen-100 text-mintGreen-600',
                                                    'rejected' => 'bg-red-100 text-red-600',
                                                    default => 'bg-gray-100 text-gray-600',
                                                } }}">
                                                {{ match($payment->status) {
                                                    'pending' => '⏳ Pending',
                                                    'approved' => '✅ Disetujui',
                                                    'rejected' => '❌ Ditolak',
                                                    default => ucfirst($payment->status),
                                                } }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-gray-500 text-xs">{{ $payment->created_at->locale('id')->isoFormat('D MMM YYYY') }}</td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('super-admin.payments.show', $payment) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-skyBlue-50 text-skyBlue-600 hover:bg-skyBlue-100 transition">
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
                        <a href="{{ route('super-admin.payments.show', $payment) }}" class="block bg-white rounded-2xl shadow-soft p-4 border border-gray-100">
                            <div class="flex items-center justify-between mb-2">
                                <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium
                                    {{ match($payment->status) {
                                        'pending' => 'bg-warmYellow-100 text-warmYellow-600',
                                        'approved' => 'bg-mintGreen-100 text-mintGreen-600',
                                        'rejected' => 'bg-red-100 text-red-600',
                                        default => 'bg-gray-100 text-gray-600',
                                    } }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                                <span class="text-sm font-bold text-gray-800">{{ $payment->getAmountFormatted() }}</span>
                            </div>
                            <p class="text-sm font-medium text-gray-800">{{ $payment->tenant->name ?? '-' }}</p>
                            <p class="text-xs text-gray-500">{{ $payment->bank_name ?? '-' }} · {{ $payment->created_at->locale('id')->diffForHumans() }}</p>
                        </a>
                    @empty
                        <div class="bg-white rounded-2xl shadow-soft p-6">
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
