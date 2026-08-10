<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <x-breadcrumb :items="[
                    ['url' => route('facility.dashboard'), 'label' => __('Dashboard')],
                    ['label' => __('Rujukan')],
                ]" />
                <h2 class="mt-2 font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                    🔄 {{ __('Rujukan') }}
                </h2>
            </div>
            <a href="{{ route('facility.referrals.create') }}" class="inline-flex items-center justify-center gap-1 px-4 py-2.5 bg-skyBlue-500 hover:bg-skyBlue-600 text-white font-medium rounded-xl text-sm transition shadow-soft min-h-[44px]">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('Buat Rujukan') }}
            </a>
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

                    <!-- Filter Tabs -->
                    <div class="mb-6 flex flex-wrap gap-2">
                        <a href="{{ route('facility.referrals.index') }}" class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-medium transition {{ !$filter ? 'bg-skyBlue-500 text-white shadow-soft' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                            {{ __('Semua') }}
                        </a>
                        <a href="{{ route('facility.referrals.index', ['status' => 'pending']) }}" class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-medium transition {{ $filter === 'pending' ? 'bg-yellow-500 text-white shadow-soft' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                            {{ __('Menunggu') }}
                        </a>
                        <a href="{{ route('facility.referrals.index', ['status' => 'accepted']) }}" class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-medium transition {{ $filter === 'accepted' ? 'bg-skyBlue-500 text-white shadow-soft' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                            {{ __('Diterima') }}
                        </a>
                        <a href="{{ route('facility.referrals.index', ['status' => 'completed']) }}" class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-medium transition {{ $filter === 'completed' ? 'bg-mintGreen-500 text-white shadow-soft' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                            {{ __('Selesai') }}
                        </a>
                        <a href="{{ route('facility.referrals.index', ['status' => 'cancelled']) }}" class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-medium transition {{ $filter === 'cancelled' ? 'bg-red-500 text-white shadow-soft' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                            {{ __('Dibatalkan') }}
                        </a>
                    </div>

                    @if ($referrals->isEmpty())
                        <x-empty-state
                            icon="🔄"
                            :title="__('Belum Ada Rujukan')"
                            :description="__('Buat rujukan pasien ke fasilitas kesehatan lain.')"
                            :action-url="route('facility.referrals.create')"
                            :action-text="__('Buat Rujukan Pertama')"
                        />
                    @else
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl">
                            <div class="overflow-x-auto -mx-4 sm:mx-0 px-4 sm:px-0">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Pasien') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Fasilitas Tujuan') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Alasan') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Status') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Tanggal') }}</th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Aksi') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach ($referrals as $referral)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-8 h-8 rounded-full bg-softPink-100 dark:bg-softPink-950/30 flex items-center justify-center text-sm">
                                                            👶
                                                        </div>
                                                        <span class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $referral->child->name ?? '-' }}</span>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                                    {{ $referral->toTenant->name ?? '-' }}
                                                </td>
                                                <td class="px-6 py-4">
                                                    <p class="text-sm text-gray-600 dark:text-gray-300 max-w-xs truncate">{{ $referral->reason }}</p>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @php
                                                        $statusColors = [
                                                            'pending' => 'bg-yellow-100 dark:bg-yellow-950/30 text-yellow-700 dark:text-yellow-400',
                                                            'accepted' => 'bg-skyBlue-100 dark:bg-skyBlue-950/30 text-skyBlue-700 dark:text-skyBlue-400',
                                                            'completed' => 'bg-mintGreen-100 dark:bg-mintGreen-950/30 text-mintGreen-700 dark:text-mintGreen-400',
                                                            'cancelled' => 'bg-red-100 dark:bg-red-950/30 text-red-700 dark:text-red-400',
                                                        ];
                                                    @endphp
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium {{ $statusColors[$referral->status->value] ?? '' }}">
                                                        {{ $referral->status->label() }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $referral->created_at->format('d M Y') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                                    <a href="{{ route('facility.referrals.show', $referral) }}" class="text-skyBlue-500 hover:text-skyBlue-600 dark:text-skyBlue-400">{{ __('Lihat') }}</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                                {{ $referrals->withQueryString()->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
