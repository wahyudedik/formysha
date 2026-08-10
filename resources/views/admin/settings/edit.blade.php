<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                ⚙️ {{ __('Pengaturan') }}
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
                    ['label' => 'Pengaturan'],
                ]" />

                <form method="POST" action="{{ route('admin.settings.update') }}" x-data="{ loading: false }" @submit="loading = true">
                    @csrf
                    @method('PUT')

                    <div class="max-w-2xl space-y-6">
                        {{-- Organization Name --}}
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-4 sm:p-6 border border-gray-100 dark:border-gray-700">
                            <h3 class="font-semibold text-gray-800 dark:text-gray-100 mb-4">🏢 {{ __('Informasi Organisasi') }}</h3>
                            <div>
                                <label for="organization_name" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Nama Organisasi</label>
                                <input
                                    type="text"
                                    id="organization_name"
                                    name="organization_name"
                                    value="{{ old('organization_name', $settings['organization_name'] ?? $tenant->name) }}"
                                    class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-skyBlue-500 focus:ring-skyBlue-500"
                                    placeholder="Nama organisasi Anda"
                                >
                                @error('organization_name')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Timezone & Language --}}
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-4 sm:p-6 border border-gray-100 dark:border-gray-700">
                            <h3 class="font-semibold text-gray-800 dark:text-gray-100 mb-4">🌍 {{ __('Lokalisasi') }}</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="timezone" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Zona Waktu</label>
                                    <select
                                        id="timezone"
                                        name="timezone"
                                        class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-skyBlue-500 focus:ring-skyBlue-500"
                                    >
                                        @php
                                            $timezones = [
                                                'Asia/Jakarta' => 'WIB (Jakarta)',
                                                'Asia/Makassar' => 'WITA (Makassar)',
                                                'Asia/Jayapura' => 'WIT (Jayapura)',
                                                'Asia/Pontianak' => 'WIB (Pontianak)',
                                            ];
                                            $currentTz = $settings['timezone'] ?? 'Asia/Jakarta';
                                        @endphp
                                        @foreach ($timezones as $tz => $label)
                                            <option value="{{ $tz }}" {{ $currentTz === $tz ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('timezone')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="language" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Bahasa</label>
                                    <select
                                        id="language"
                                        name="language"
                                        class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-skyBlue-500 focus:ring-skyBlue-500"
                                    >
                                        @php
                                            $languages = [
                                                'id' => 'Bahasa Indonesia',
                                                'en' => 'English',
                                            ];
                                            $currentLang = $settings['language'] ?? 'id';
                                        @endphp
                                        @foreach ($languages as $code => $name)
                                            <option value="{{ $code }}" {{ $currentLang === $code ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error('language')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Submit --}}
                        <div class="flex justify-end">
                            <button type="submit" x-bind:disabled="loading" class="btn-primary min-h-[44px] disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center justify-center gap-2">
                                <svg x-show="loading" class="w-4 h-4 shrink-0 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span x-text="loading ? '{{ __('Menyimpan...') }}' : '💾 {{ __('Simpan Pengaturan') }}'"></span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
