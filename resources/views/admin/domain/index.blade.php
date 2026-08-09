<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                🌐 Custom Domain
            </h2>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-6">
            @include('admin.partials.sidebar')

            <div class="flex-1 min-w-0">
                <x-breadcrumb :items="[
                    ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
                    ['label' => 'Custom Domain'],
                ]" />

                {{-- Status Card --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-4 sm:p-6 border border-gray-100 dark:border-gray-700 mb-6">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">📋 Status Domain</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Custom Domain</p>
                            <p class="font-semibold text-gray-800 dark:text-gray-100">
                                {{ $domainStatus['custom_domain'] ?? 'Belum dikonfigurasi' }}
                            </p>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Status Verifikasi</p>
                            @if($domainStatus['is_verified'])
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-mintGreen-100 dark:bg-mintGreen-950/30 text-mintGreen-700 dark:text-mintGreen-400">
                                    ✅ Terverifikasi
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-warmYellow-100 dark:bg-warmYellow-950/30 text-warmYellow-700 dark:text-warmYellow-400">
                                    ⏳ Menunggu Verifikasi
                                </span>
                            @endif
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Waktu Verifikasi</p>
                            <p class="font-semibold text-gray-800 dark:text-gray-100">
                                {{ $domainStatus['verified_at'] ? \Carbon\Carbon::parse($domainStatus['verified_at'])->format('d M Y H:i') : '-' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Set Domain Form --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-4 sm:p-6 border border-gray-100 dark:border-gray-700 mb-6">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">🔧 Atur Custom Domain</h3>

                    <form method="POST" action="{{ route('admin.domain.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="custom_domain" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Custom Domain</label>
                            <input
                                type="text"
                                id="custom_domain"
                                name="custom_domain"
                                value="{{ $domainStatus['custom_domain'] ?? '' }}"
                                placeholder="anak.kliniksehat.id"
                                class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-skyBlue-500 focus:ring-skyBlue-500 text-sm"
                            />
                            @error('custom_domain')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Masukkan domain tanpa protokol (http/https) dan tanpa www.
                            </p>
                        </div>

                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                            <button type="submit" class="btn-primary text-sm min-h-[44px]">
                                💾 Simpan Domain
                            </button>

                            @if($domainStatus['custom_domain'])
                                <form method="POST" action="{{ route('admin.domain.verify') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="btn-accent text-sm min-h-[44px]">
                                        🔍 Verifikasi DNS
                                    </button>
                                </form>
                            @endif
                        </div>
                    </form>
                </div>

                {{-- DNS Instructions --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-4 sm:p-6 border border-gray-100 dark:border-gray-700 mb-6">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">📖 Instruksi DNS</h3>

                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                        Untuk mengaktifkan custom domain, tambahkan record DNS berikut di penyedia domain Anda:
                    </p>

                    <div class="overflow-x-auto -mx-4 sm:mx-0 px-4 sm:px-0">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Record Type</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Name</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Value</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-100">CNAME</td>
                                    <td class="px-4 py-3 font-mono text-xs text-gray-600 dark:text-gray-300">{{ $domainStatus['custom_domain'] ?? '@' }}</td>
                                    <td class="px-4 py-3 font-mono text-xs text-gray-600 dark:text-gray-300">app.formysha.my.id</td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">Aplikasi (CNAME)</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-100">ATAU</td>
                                    <td class="px-4 py-3" colspan="3"></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-100">A</td>
                                    <td class="px-4 py-3 font-mono text-xs text-gray-600 dark:text-gray-300">{{ $domainStatus['custom_domain'] ?? '@' }}</td>
                                    <td class="px-4 py-3 font-mono text-xs text-gray-600 dark:text-gray-300">{{ config('services.domain.expected_ip', '127.0.0.1') }}</td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">Aplikasi (A record)</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 p-4 bg-skyBlue-50 dark:bg-skyBlue-950/30 rounded-xl">
                        <p class="text-sm text-skyBlue-700 dark:text-skyBlue-400">
                            💡 <strong>Catatan:</strong> Setelah menambahkan record DNS, tunggu beberapa menit hingga DNS propagation selesai, lalu klik tombol "Verifikasi DNS".
                        </p>
                    </div>
                </div>

                {{-- Remove Domain --}}
                @if($domainStatus['custom_domain'])
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-4 sm:p-6 border border-red-100 dark:border-red-900/30">
                        <h3 class="text-lg font-bold text-red-600 dark:text-red-400 mb-2">⚠️ Hapus Custom Domain</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                            Menghapus custom domain akan mengembalikan akses ke URL default ForMysha.
                        </p>
                        <button type="button"
                            x-data
                            x-on:click.prevent="$dispatch('delete-confirm', 'delete-domain')"
                            class="min-h-[44px] px-4 py-2 bg-red-500 text-white rounded-xl text-sm font-medium hover:bg-red-600 transition-colors">
                            🗑️ Hapus Domain
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-confirm-delete
        id="delete-domain"
        title="{{ __('Hapus Custom Domain') }}"
        message="{{ __('Apakah Anda yakin ingin menghapus custom domain? Domain akan dikembalikan ke URL default ForMysha.') }}"
        action="{{ route('admin.domain.remove') }}"
    />
</x-app-layout>
