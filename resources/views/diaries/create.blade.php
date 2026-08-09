<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('diaries.index', $child) }}" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                📝 {{ __('Tulis Diary') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl">
                <div class="p-6 sm:p-8">
                    <x-child-selector :children="$children" :child="$child" :route-name="'diaries.create'" />

                    <!-- Child Info -->
                    <div class="flex items-center gap-3 mb-6 p-4 bg-gradient-to-r from-peach-50 to-warmYellow-50 dark:from-peach-950/30 dark:to-warmYellow-950/30 rounded-2xl">
                        <div class="w-10 h-10 rounded-xl {{ $child->gender === 'female' ? 'bg-softPink-100 dark:bg-softPink-950/30' : 'bg-skyBlue-100 dark:bg-skyBlue-950/30' }} flex items-center justify-center text-lg">
                            {{ $child->gender === 'female' ? '👧' : '👦' }}
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Diary untuk') }}</p>
                            <p class="font-semibold text-gray-800 dark:text-gray-100">{{ $child->nickname ?? $child->name }}</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('diaries.store', $child) }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Title -->
                        <div class="mb-5">
                            <x-input-label for="title" :value="__('Judul Catatan')" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title')" required autofocus placeholder="Contoh: Hari yang Menyenangkan" />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <!-- Diary Date -->
                        <div class="mb-5">
                            <x-input-label for="diary_date" :value="__('Tanggal')" />
                            <x-text-input id="diary_date" name="diary_date" type="date" class="mt-1 block w-full" :value="old('diary_date', date('Y-m-d'))" required />
                            <x-input-error :messages="$errors->get('diary_date')" class="mt-2" />
                        </div>

                        <!-- Content -->
                        <div class="mb-5">
                            <x-input-label for="content" :value="__('Cerita Hari Ini')" />
                            <textarea id="content" name="content" rows="8" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-peach-500 focus:ring-peach-500 rounded-2xl shadow-soft" placeholder="Ceritakan tentang hari ini..." required>{{ old('content') }}</textarea>
                            <x-input-error :messages="$errors->get('content')" class="mt-2" />
                        </div>

                        <!-- Mood & Weather -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                            <div>
                                <x-input-label for="mood" :value="__('Mood (opsional)')" />
                                <select id="mood" name="mood" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-peach-500 focus:ring-peach-500 rounded-2xl shadow-soft">
                                    <option value="">{{ __('Pilih mood...') }}</option>
                                    <option value="happy" {{ old('mood') === 'happy' ? 'selected' : '' }}>😊 Bahagia</option>
                                    <option value="excited" {{ old('mood') === 'excited' ? 'selected' : '' }}>🤩 Antusias</option>
                                    <option value="calm" {{ old('mood') === 'calm' ? 'selected' : '' }}>😌 Tenang</option>
                                    <option value="sad" {{ old('mood') === 'sad' ? 'selected' : '' }}>😢 Sedih</option>
                                    <option value="surprised" {{ old('mood') === 'surprised' ? 'selected' : '' }}>😲 Terkejut</option>
                                    <option value="loved" {{ old('mood') === 'loved' ? 'selected' : '' }}>🥰 Disayang</option>
                                </select>
                                <x-input-error :messages="$errors->get('mood')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="weather" :value="__('Cuaca (opsional)')" />
                                <select id="weather" name="weather" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-peach-500 focus:ring-peach-500 rounded-2xl shadow-soft">
                                    <option value="">{{ __('Pilih cuaca...') }}</option>
                                    <option value="sunny" {{ old('weather') === 'sunny' ? 'selected' : '' }}>☀️ Cerah</option>
                                    <option value="cloudy" {{ old('weather') === 'cloudy' ? 'selected' : '' }}>☁️ Berawan</option>
                                    <option value="rainy" {{ old('weather') === 'rainy' ? 'selected' : '' }}>🌧️ Hujan</option>
                                    <option value="windy" {{ old('weather') === 'windy' ? 'selected' : '' }}>💨 Berangin</option>
                                    <option value="snowy" {{ old('weather') === 'snowy' ? 'selected' : '' }}>❄️ Bersalju</option>
                                </select>
                                <x-input-error :messages="$errors->get('weather')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Media Upload -->
                        <x-media-upload name="media[]" :multiple="true" />

                        <!-- Privacy -->
                        <div class="mb-6" x-data="{ checked: {{ old('is_private', 'true') === 'true' || old('is_private') === '1' ? 'true' : 'false' }} }">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="hidden" name="is_private" value="0">
                                <input type="checkbox" name="is_private" value="1" x-model="checked" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-peach-500 focus:ring-peach-500">
                                <span class="text-sm text-gray-700 dark:text-gray-300">🔒 {{ __('Tandai sebagai privat') }}</span>
                            </label>
                        </div>

                        <!-- Submit -->
                        <div class="flex items-center gap-3">
                            <a href="{{ route('diaries.index', $child) }}" class="btn-secondary">
                                {{ __('Batal') }}
                            </a>
                            <button type="submit" class="btn-primary">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ __('Simpan Catatan') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
