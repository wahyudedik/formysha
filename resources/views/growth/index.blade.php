<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('children.show', $child) }}" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    📏 {{ __('My Growth') }} — {{ $child->nickname ?? $child->name }}
                </h2>
            </div>
            <a href="{{ route('growth.create', $child) }}" class="btn-primary text-sm">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('Tambah Pengukuran') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 p-4 bg-mintGreen-50 border border-mintGreen-200 text-mintGreen-700 rounded-xl" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Latest Growth Summary -->
            @if ($latestGrowth)
                <div class="mb-8 grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="bg-white overflow-hidden shadow-soft sm:rounded-2xl p-5">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-softPink-100 to-softPink-50 flex items-center justify-center text-2xl">
                                ⚖️
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">{{ __('Berat Badan') }}</p>
                                <p class="text-xl font-bold text-gray-800">{{ $latestGrowth->weight_label ?? '—' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-soft sm:rounded-2xl p-5">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-skyBlue-100 to-skyBlue-50 flex items-center justify-center text-2xl">
                                📐
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">{{ __('Tinggi Badan') }}</p>
                                <p class="text-xl font-bold text-gray-800">{{ $latestGrowth->height_label ?? '—' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-soft sm:rounded-2xl p-5">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-mintGreen-100 to-mintGreen-50 flex items-center justify-center text-2xl">
                                🧠
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">{{ __('Lingkar Kepala') }}</p>
                                <p class="text-xl font-bold text-gray-800">{{ $latestGrowth->head_circumference_label ?? '—' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Growth Chart -->
            @if ($growthHistory->count() >= 2)
                <div class="mb-8 bg-white overflow-hidden shadow-soft sm:rounded-3xl">
                    <div class="p-6">
                        <h3 class="font-semibold text-gray-800 mb-4">📈 {{ __('Grafik Pertumbuhan') }}</h3>
                        <x-growth-chart :growths="$growthHistory" />
                    </div>
                </div>
            @endif

            @if ($growths->isEmpty())
                <!-- Empty State -->
                <div class="text-center py-16">
                    <div class="text-6xl mb-4">📏</div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">{{ __('Belum Ada Data Pertumbuhan') }}</h3>
                    <p class="text-gray-500 mb-6">{{ __('Mulai catat pertumbuhan ' . ($child->nickname ?? $child->name) . ' untuk memantau perkembangannya.') }}</p>
                    <a href="{{ route('growth.create', $child) }}" class="btn-primary">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        {{ __('Tambah Pengukuran Pertama') }}
                    </a>
                </div>
            @else
                <!-- Growth History Table -->
                <div class="bg-white overflow-hidden shadow-soft sm:rounded-3xl">
                    <div class="p-6">
                        <h3 class="font-semibold text-gray-800 mb-4">📋 {{ __('Riwayat Pengukuran') }}</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-100">
                                        <th class="text-left py-3 px-4 font-medium text-gray-500">{{ __('Tanggal') }}</th>
                                        <th class="text-left py-3 px-4 font-medium text-gray-500">{{ __('Berat') }}</th>
                                        <th class="text-left py-3 px-4 font-medium text-gray-500">{{ __('Tinggi') }}</th>
                                        <th class="text-left py-3 px-4 font-medium text-gray-500">{{ __('Lingkar Kepala') }}</th>
                                        <th class="text-left py-3 px-4 font-medium text-gray-500">{{ __('Catatan') }}</th>
                                        <th class="text-right py-3 px-4 font-medium text-gray-500">{{ __('Aksi') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($growths as $growth)
                                        <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                                            <td class="py-3 px-4 text-gray-800 font-medium">{{ $growth->formatted_date }}</td>
                                            <td class="py-3 px-4 text-gray-600">{{ $growth->weight_label ?? '—' }}</td>
                                            <td class="py-3 px-4 text-gray-600">{{ $growth->height_label ?? '—' }}</td>
                                            <td class="py-3 px-4 text-gray-600">{{ $growth->head_circumference_label ?? '—' }}</td>
                                            <td class="py-3 px-4 text-gray-500 max-w-[200px] truncate">{{ $growth->notes ?? '—' }}</td>
                                            <td class="py-3 px-4 text-right">
                                                <div class="flex items-center justify-end gap-2">
                                                    <a href="{{ route('growth.edit', [$child, $growth]) }}" class="text-skyBlue-600 hover:text-skyBlue-700 text-xs font-medium">
                                                        ✏️ {{ __('Edit') }}
                                                    </a>
                                                    <form method="POST" action="{{ route('growth.destroy', [$child, $growth]) }}" x-data>
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-500 hover:text-red-600 text-xs font-medium" x-data x-on:click.prevent="if(confirm('Yakin ingin menghapus data pengukuran ini?')) $el.closest('form').submit()">
                                                            🗑️ {{ __('Hapus') }}
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $growths->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
