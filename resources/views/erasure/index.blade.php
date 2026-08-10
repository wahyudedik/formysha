<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('profile.edit') }}" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                    {{ __('Hak Penghapusan Data') }}
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

            {{-- Warning Banner --}}
            <div class="mb-6 p-4 bg-softPink-50 dark:bg-softPink-950/30 border border-softPink-200 dark:border-softPink-800 rounded-xl">
                <div class="flex items-start gap-3">
                    <span class="text-2xl mt-0.5">⚠️</span>
                    <div>
                        <h3 class="font-semibold text-softPink-800 dark:text-softPink-300 text-sm">{{ __('Peringatan Penting') }}</h3>
                        <p class="text-softPink-700 dark:text-softPink-400 text-sm mt-1">
                            {{ __('Penghapusan data bersifat permanen dan tidak dapat dibatalkan. Pastikan Anda telah mencadangkan data yang penting sebelum melanjutkan.') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Data Summary --}}
            <div class="mb-6 p-4 bg-white dark:bg-gray-800 shadow-soft sm:rounded-2xl">
                <h3 class="font-semibold text-gray-800 dark:text-gray-100 mb-3">{{ __('Ringkasan Data Anda') }}</h3>
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
                    <div class="text-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                        <div class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $userSummary['children'] }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('Anak') }}</div>
                    </div>
                    <div class="text-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                        <div class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $userSummary['family_members'] }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('Keluarga') }}</div>
                    </div>
                    <div class="text-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                        <div class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $userSummary['consents'] }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('Consent') }}</div>
                    </div>
                    <div class="text-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                        <div class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $userSummary['notifications'] }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('Notifikasi') }}</div>
                    </div>
                    <div class="text-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                        <div class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $userSummary['audit_logs'] }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('Audit Log') }}</div>
                    </div>
                </div>
            </div>

            {{-- Per-Child Deletion --}}
            @if ($children->isNotEmpty())
                <div class="mb-6">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-100 mb-3">{{ __('Hapus Data per Anak') }}</h3>
                    <div class="space-y-4">
                        @foreach ($childSummaries as $childId => $data)
                            @php
                                $child = $data['child'];
                                $summary = $data['summary'];
                            @endphp
                            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-2xl p-4 sm:p-6"
                                 x-data="{ open: false, loading: false }">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-softPink-50 dark:bg-softPink-950/30 flex items-center justify-center text-lg">
                                            👶
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-800 dark:text-gray-100 text-sm sm:text-base">
                                                {{ $child->nickname ?? $child->name }}
                                            </h4>
                                            <p class="text-gray-500 dark:text-gray-400 text-xs">
                                                {{ $summary['timelines'] }} {{ __('timeline') }},
                                                {{ $summary['albums'] }} {{ __('album') }},
                                                {{ $summary['media'] }} {{ __('media') }},
                                                {{ $summary['diaries'] }} {{ __('diary') }},
                                                {{ $summary['documents'] }} {{ __('dokumen') }}
                                            </p>
                                        </div>
                                    </div>
                                    <button @click="open = true"
                                            class="btn-secondary text-sm min-h-[44px] whitespace-nowrap text-red-600 border-red-200 hover:bg-red-50 dark:text-red-400 dark:border-red-800 dark:hover:bg-red-950/30">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        {{ __('Hapus') }}
                                    </button>
                                </div>

                                {{-- Delete Confirmation --}}
                                <div x-show="open" x-transition class="mt-4 p-4 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800 rounded-xl">
                                    <p class="text-red-700 dark:text-red-300 text-sm mb-3">
                                        {{ __('Masukkan password Anda untuk menghapus semua data') }} <strong>{{ $child->name }}</strong>.
                                    </p>
                                    <form method="POST" action="{{ route('erasure.destroyChild', $child) }}" x-on:submit="loading = true">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                                            <input type="password" name="password" required
                                                   placeholder="{{ __('Password') }}"
                                                   class="text-input flex-1 text-sm min-h-[44px]">
                                            <div class="flex gap-2">
                                                <button type="button" @click="open = false"
                                                        class="btn-secondary text-sm min-h-[44px]">
                                                    {{ __('Batal') }}
                                                </button>
                                                <button type="submit" :disabled="loading"
                                                        class="btn-primary text-sm min-h-[44px] bg-red-600 hover:bg-red-700">
                                                    <span x-show="!loading">{{ __('Hapus Permanen') }}</span>
                                                    <span x-show="loading" x-cloak class="flex items-center gap-2">
                                                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                                        </svg>
                                                        {{ __('Menghapus...') }}
                                                    </span>
                                                </button>
                                            </div>
                                        </div>
                                        @error('password')
                                            <p class="text-red-600 dark:text-red-400 text-xs mt-2">{{ $message }}</p>
                                        @enderror
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Full Account Deletion --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-2xl p-4 sm:p-6 border-2 border-red-200 dark:border-red-800"
                 x-data="{ open: false, loading: false }">
                <div class="flex items-start gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-red-50 dark:bg-red-950/30 flex items-center justify-center text-lg shrink-0">
                        🗑️
                    </div>
                    <div>
                        <h3 class="font-semibold text-red-700 dark:text-red-300 text-sm sm:text-base">
                            {{ __('Hapus Akun dan Semua Data') }}
                        </h3>
                        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">
                            {{ __('Menghapus akun Anda dan seluruh data yang terkait secara permanen. Termasuk semua data anak, foto, dokumen, dan catatan.') }}
                        </p>
                    </div>
                </div>

                <button @click="open = true"
                        class="w-full sm:w-auto btn-secondary text-sm min-h-[44px] text-red-600 border-red-200 hover:bg-red-50 dark:text-red-400 dark:border-red-800 dark:hover:bg-red-950/30">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    {{ __('Hapus Akun Saya') }}
                </button>

                {{-- Delete Account Confirmation --}}
                <div x-show="open" x-transition class="mt-4 p-4 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800 rounded-xl">
                    <p class="text-red-700 dark:text-red-300 text-sm mb-3">
                        {{ __('Masukkan password Anda dan ketik') }} <strong>HAPUS AKUN SAYA</strong> {{ __('untuk mengonfirmasi.') }}
                    </p>
                    <form method="POST" action="{{ route('erasure.destroyAccount') }}" x-on:submit="loading = true">
                        @csrf
                        @method('DELETE')
                        <div class="space-y-3">
                            <div>
                                <label for="delete-password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    {{ __('Password') }}
                                </label>
                                <input type="password" id="delete-password" name="password" required
                                       placeholder="{{ __('Masukkan password Anda') }}"
                                       class="text-input w-full text-sm min-h-[44px]">
                                @error('password')
                                    <p class="text-red-600 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="delete-confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    {{ __('Ketik') }} <strong>HAPUS AKUN SAYA</strong>
                                </label>
                                <input type="text" id="delete-confirmation" name="confirmation" required
                                       placeholder="HAPUS AKUN SAYA"
                                       class="text-input w-full text-sm min-h-[44px]">
                                @error('confirmation')
                                    <p class="text-red-600 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                                <button type="button" @click="open = false"
                                        class="btn-secondary text-sm min-h-[44px]">
                                    {{ __('Batal') }}
                                </button>
                                <button type="submit" :disabled="loading"
                                        class="btn-primary text-sm min-h-[44px] bg-red-600 hover:bg-red-700">
                                    <span x-show="!loading">{{ __('Hapus Akun Permanen') }}</span>
                                    <span x-show="loading" x-cloak class="flex items-center gap-2">
                                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                        </svg>
                                        {{ __('Menghapus...') }}
                                    </span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
