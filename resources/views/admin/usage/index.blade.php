<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                📊 {{ __('Penggunaan') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-6">
            {{-- Sidebar --}}
            @include('admin.partials.sidebar')

            {{-- Main Content --}}
            <div class="flex-1 min-w-0">
                <x-breadcrumb :items="[
                    ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
                    ['label' => 'Penggunaan'],
                ]" />

                {{-- Plan Info --}}
                @if ($plan)
                    <div class="bg-gradient-to-r from-skyBlue-50 to-lavender-50 dark:from-skyBlue-950/30 dark:to-lavender-950/30 rounded-2xl shadow-soft p-4 sm:p-6 mb-6 border border-skyBlue-100 dark:border-skyBlue-900/30">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div>
                                <h3 class="font-semibold text-gray-800 dark:text-gray-100">Paket: {{ $plan->name }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $plan->getPriceMonthlyFormatted() }}/bulan</p>
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-medium bg-mintGreen-100 dark:bg-mintGreen-950/30 text-mintGreen-700 dark:text-mintGreen-400">
                                ✅ Aktif
                            </span>
                        </div>
                    </div>
                @endif

                {{-- Usage Cards --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    {{-- Children --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-4 sm:p-6 border border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 rounded-xl bg-softPink-50 dark:bg-softPink-950/30 flex items-center justify-center shrink-0">
                                <span class="text-2xl">👶</span>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800 dark:text-gray-100">Anak</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $childrenCount }} / {{ $maxChildren === -1 ? 'Unlimited' : $maxChildren }}
                                </p>
                            </div>
                        </div>
                        @if ($maxChildren > 0)
                            @php
                                $childrenPercentage = min(100, ($childrenCount / $maxChildren) * 100);
                            @endphp
                            <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-3">
                                <div
                                    class="h-3 rounded-full bg-gradient-to-r from-mintGreen-400 to-skyBlue-400 transition-all duration-500"
                                    style="width: {{ $childrenPercentage }}%"
                                ></div>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                {{ round($childrenPercentage) }}% terpakai
                            </p>
                        @else
                            <p class="text-xs text-mintGreen-600 dark:text-mintGreen-400 mt-2">♾️ Unlimited</p>
                        @endif
                    </div>

                    {{-- Photos --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-4 sm:p-6 border border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 rounded-xl bg-skyBlue-50 dark:bg-skyBlue-950/30 flex items-center justify-center shrink-0">
                                <span class="text-2xl">📷</span>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800 dark:text-gray-100">Foto</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $photoCount }} / {{ $maxPhotos === -1 ? 'Unlimited' : number_format($maxPhotos) }}
                                </p>
                            </div>
                        </div>
                        @if ($maxPhotos > 0)
                            @php
                                $photoPercentage = min(100, ($photoCount / $maxPhotos) * 100);
                            @endphp
                            <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-3">
                                <div
                                    class="h-3 rounded-full bg-gradient-to-r from-mintGreen-400 to-skyBlue-400 transition-all duration-500"
                                    style="width: {{ $photoPercentage }}%"
                                ></div>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                {{ round($photoPercentage) }}% terpakai
                            </p>
                        @else
                            <p class="text-xs text-mintGreen-600 dark:text-mintGreen-400 mt-2">♾️ Unlimited</p>
                        @endif
                    </div>

                    {{-- Videos --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-4 sm:p-6 border border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 rounded-xl bg-lavender-50 dark:bg-lavender-950/30 flex items-center justify-center shrink-0">
                                <span class="text-2xl">🎬</span>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800 dark:text-gray-100">Video</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $videoCount }} / {{ $maxVideos === -1 ? 'Unlimited' : number_format($maxVideos) }}
                                </p>
                            </div>
                        </div>
                        @if ($maxVideos > 0)
                            @php
                                $videoPercentage = min(100, ($videoCount / $maxVideos) * 100);
                            @endphp
                            <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-3">
                                <div
                                    class="h-3 rounded-full bg-gradient-to-r from-mintGreen-400 to-skyBlue-400 transition-all duration-500"
                                    style="width: {{ $videoPercentage }}%"
                                ></div>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                {{ round($videoPercentage) }}% terpakai
                            </p>
                        @else
                            <p class="text-xs text-mintGreen-600 dark:text-mintGreen-400 mt-2">♾️ Unlimited</p>
                        @endif
                    </div>

                    {{-- Storage --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-4 sm:p-6 border border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 rounded-xl bg-warmYellow-50 dark:bg-warmYellow-950/30 flex items-center justify-center shrink-0">
                                <span class="text-2xl">💾</span>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800 dark:text-gray-100">Penyimpanan</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    @if ($maxStorageMb === -1)
                                        {{ \App\Models\Media::where('tenant_id', $tenant->id)->sum('file_size') > 0 ? number_format(round($storageUsed / 1048576, 2), 2) : '0' }} MB / Unlimited
                                    @else
                                        {{ number_format(round($storageUsed / 1048576, 2), 2) }} MB / {{ $maxStorageMb }} MB
                                    @endif
                                </p>
                            </div>
                        </div>
                        @if ($maxStorageMb > 0)
                            @php
                                $storagePercentage = min(100, ($storageUsed / $maxStorageBytes) * 100);
                            @endphp
                            <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-3">
                                <div
                                    class="h-3 rounded-full bg-gradient-to-r from-mintGreen-400 to-skyBlue-400 transition-all duration-500"
                                    style="width: {{ $storagePercentage }}%"
                                ></div>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                {{ round($storagePercentage) }}% terpakai
                            </p>
                        @else
                            <p class="text-xs text-mintGreen-600 dark:text-mintGreen-400 mt-2">♾️ Unlimited</p>
                        @endif
                    </div>
                </div>

                {{-- Upgrade CTA --}}
                @if ($plan && ($childrenCount / max($maxChildren, 1) > 0.8 || $photoCount / max($maxPhotos, 1) > 0.8 || $videoCount / max($maxVideos, 1) > 0.8 || ($maxStorageMb > 0 && $storageUsed / $maxStorageBytes > 0.8)))
                    <div class="mt-6 bg-gradient-to-r from-warmYellow-50 to-peach-50 dark:from-warmYellow-950/30 dark:to-peach-950/30 rounded-2xl shadow-soft p-4 sm:p-6 border border-warmYellow-100 dark:border-warmYellow-900/30">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-warmYellow-100 dark:bg-warmYellow-950/30 flex items-center justify-center shrink-0">
                                <span class="text-2xl">⬆️</span>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-800 dark:text-gray-100">Mendekati Batas?</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Paket Anda hampir mencapai batas. Upgrade untuk mendapatkan lebih banyak ruang dan fitur.</p>
                            </div>
                            <a href="{{ route('subscription.plans') }}" class="shrink-0 btn-primary text-sm min-h-[44px] inline-flex items-center">
                                Upgrade Sekarang
                            </a>
                        </div>
                    </div>
                @endif

                {{-- No Plan CTA --}}
                @if (! $plan)
                    <div class="mt-6 bg-gradient-to-r from-skyBlue-50 to-lavender-50 dark:from-skyBlue-950/30 dark:to-lavender-950/30 rounded-2xl shadow-soft p-4 sm:p-6 border border-skyBlue-100 dark:border-skyBlue-900/30">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-skyBlue-100 dark:bg-skyBlue-950/30 flex items-center justify-center shrink-0">
                                <span class="text-2xl">📋</span>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-800 dark:text-gray-100">Belum Ada Paket</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Pilih paket langganan untuk mulai menggunakan ForMysha.</p>
                            </div>
                            <a href="{{ route('subscription.plans') }}" class="shrink-0 btn-primary text-sm min-h-[44px] inline-flex items-center">
                                Lihat Paket
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
