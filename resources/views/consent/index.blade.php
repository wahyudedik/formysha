<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('children.show', $child) }}" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                    {{ __('Pengaturan Privasi') }} — {{ $child->nickname ?? $child->name }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Success Message --}}
            @if (session('success'))
                <div class="mb-6 p-4 bg-mintGreen-50 border border-mintGreen-200 text-mintGreen-700 dark:bg-mintGreen-950/30 dark:border-mintGreen-800 dark:text-mintGreen-400 rounded-xl"
                     x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 4000)">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            {{-- Privacy Info --}}
            <div class="mb-6 p-4 bg-skyBlue-50 dark:bg-skyBlue-950/30 border border-skyBlue-200 dark:border-skyBlue-800 rounded-xl">
                <div class="flex items-start gap-3">
                    <span class="text-2xl mt-0.5">🔒</span>
                    <div>
                        <h3 class="font-semibold text-skyBlue-800 dark:text-skyBlue-300 text-sm">{{ __('Tentang Privasi Data') }}</h3>
                        <p class="text-skyBlue-700 dark:text-skyBlue-400 text-sm mt-1">
                            {{ __('Kelola izin penggunaan data untuk') }} {{ $child->nickname ?? $child->name }}.
                            {{ __('Anda dapat mengaktifkan atau menonaktifkan setiap jenis izin sesuai kebutuhan.') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Consent Cards --}}
            <div class="space-y-4">
                @foreach ($statuses as $key => $status)
                    @php
                        $type = $status['type'];
                        $isGranted = $status['granted'];
                        $consent = $status['consent'];
                    @endphp
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-2xl p-4 sm:p-6 transition-all duration-200 hover:shadow-md"
                         x-data="{ loading: false }">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            {{-- Info --}}
                            <div class="flex items-start gap-3 flex-1 min-w-0">
                                <div class="shrink-0 w-10 h-10 rounded-xl flex items-center justify-center text-lg
                                    {{ $isGranted
                                        ? 'bg-mintGreen-50 dark:bg-mintGreen-950/30'
                                        : 'bg-gray-100 dark:bg-gray-700' }}">
                                    @if ($isGranted)
                                        ✅
                                    @else
                                        ⬜
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h3 class="font-semibold text-gray-800 dark:text-gray-100 text-sm sm:text-base">
                                            {{ $type->label() }}
                                        </h3>
                                        @if ($type->isSensitive())
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-softPink-50 dark:bg-softPink-950/30 text-softPink-600 dark:text-softPink-400">
                                                {{ __('Sensitif') }}
                                            </span>
                                        @endif
                                        @if ($isGranted)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-mintGreen-50 dark:bg-mintGreen-950/30 text-mintGreen-600 dark:text-mintGreen-400">
                                                {{ __('Aktif') }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                                                {{ __('Nonaktif') }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">
                                        {{ $type->description() }}
                                    </p>
                                    @if ($consent)
                                        <p class="text-gray-400 dark:text-gray-500 text-xs mt-1">
                                            {{ __('Terakhir diubah') }}: {{ $consent->updated_at->diffForHumans() }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            {{-- Toggle Button --}}
                            <div class="shrink-0">
                                @if ($isGranted)
                                    <form method="POST" action="{{ route('consent.update', $child) }}" x-on:submit="loading = true">
                                        @csrf
                                        <input type="hidden" name="consent_type" value="{{ $key }}">
                                        <input type="hidden" name="action" value="revoke">
                                        <button type="submit"
                                                :disabled="loading"
                                                class="btn-secondary text-sm min-h-[44px] whitespace-nowrap">
                                            <span x-show="!loading">
                                                <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                </svg>
                                                {{ __('Cabut') }}
                                            </span>
                                            <span x-show="loading" x-cloak class="flex items-center gap-2">
                                                <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                                </svg>
                                                {{ __('Memproses...') }}
                                            </span>
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('consent.update', $child) }}" x-on:submit="loading = true">
                                        @csrf
                                        <input type="hidden" name="consent_type" value="{{ $key }}">
                                        <input type="hidden" name="action" value="grant">
                                        <button type="submit"
                                                :disabled="loading"
                                                class="btn-primary text-sm min-h-[44px] whitespace-nowrap">
                                            <span x-show="!loading">
                                                <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                {{ __('Berikan Izin') }}
                                            </span>
                                            <span x-show="loading" x-cloak class="flex items-center gap-2">
                                                <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                                </svg>
                                                {{ __('Memproses...') }}
                                            </span>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Summary --}}
            <div class="mt-6 p-4 bg-white dark:bg-gray-800 shadow-soft sm:rounded-2xl">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __('Ringkasan') }}
                    </span>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">
                        @php
                            $grantedCount = collect($statuses)->filter(fn ($s) => $s['granted'])->count();
                            $totalCount = count($statuses);
                        @endphp
                        {{ $grantedCount }}/{{ $totalCount }} {{ __('izin aktif') }}
                    </span>
                </div>
                <div class="mt-2 w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2">
                    <div class="bg-mintGreen-500 dark:bg-mintGreen-400 h-2 rounded-full transition-all duration-500"
                         style="width: {{ $totalCount > 0 ? ($grantedCount / $totalCount) * 100 : 0 }}%"></div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
