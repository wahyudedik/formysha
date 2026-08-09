<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('timeline.index', $child) }}" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                ✨ {{ __('Tambah Kenangan') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl">
                <div class="p-4 sm:p-6 lg:p-8">
                    <x-child-selector :children="$children" :child="$child" :route-name="'timeline.create'" />

                    <!-- Child Info -->
                    <div class="flex items-center gap-3 mb-6 p-4 bg-gradient-to-r from-lavender-50 to-softPink-50 dark:from-lavender-950/30 dark:to-softPink-950/30 rounded-2xl">
                        <div class="w-10 h-10 rounded-xl {{ $child->gender === 'female' ? 'bg-softPink-100 dark:bg-softPink-950/30' : 'bg-skyBlue-100 dark:bg-skyBlue-950/30' }} flex items-center justify-center text-lg">
                            {{ $child->gender === 'female' ? '👧' : '👦' }}
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Kenangan untuk') }}</p>
                            <p class="font-semibold text-gray-800 dark:text-gray-100">{{ $child->nickname ?? $child->name }}</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('timeline.store', $child) }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Title -->
                        <div class="mb-5">
                            <x-input-label for="title" :value="__('Judul Kenangan')" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title')" required autofocus placeholder="Contoh: Hari Pertama Sekolah" />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div class="mb-5">
                            <x-input-label for="description" :value="__('Cerita / Deskripsi')" />
                            <textarea id="description" name="description" rows="4" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-lavender-500 focus:ring-lavender-500 rounded-2xl shadow-soft" placeholder="Ceritakan momen berharga ini...">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- Event Date & Time -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                            <div>
                                <x-input-label for="event_date" :value="__('Tanggal Kejadian')" />
                                <x-text-input id="event_date" name="event_date" type="date" class="mt-1 block w-full" :value="old('event_date', date('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('event_date')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="event_time" :value="__('Waktu (opsional)')" />
                                <x-text-input id="event_time" name="event_time" type="time" class="mt-1 block w-full" :value="old('event_time')" />
                                <x-input-error :messages="$errors->get('event_time')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="mb-5">
                            <x-input-label for="location" :value="__('Lokasi (opsional)')" />
                            <x-text-input id="location" name="location" type="text" class="mt-1 block w-full" :value="old('location')" placeholder="Contoh: Jakarta, Rumah, Sekolah" />
                            <x-input-error :messages="$errors->get('location')" class="mt-2" />
                        </div>

                        <!-- Mood -->
                        <div class="mb-5">
                            <x-input-label for="mood" :value="__('Mood (opsional)')" />
                            <select id="mood" name="mood" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-lavender-500 focus:ring-lavender-500 rounded-2xl shadow-soft">
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

                        <!-- Tags -->
                        <div class="mb-5">
                            <x-input-label for="tags" :value="__('Tag (opsional)')" />
                            <x-text-input id="tags" name="tags" type="text" class="mt-1 block w-full" :value="old('tags', is_array($tags ?? null) ? implode(', ', $tags) : '')" placeholder="Pisahkan dengan koma: milestone, keluarga, sekolah" />
                            <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">{{ __('Pisahkan setiap tag dengan koma') }}</p>
                            <x-input-error :messages="$errors->get('tags')" class="mt-2" />
                        </div>

                        <!-- Media Upload -->
                        <x-media-upload name="media[]" :multiple="true" />

                        <!-- Featured -->
                        <div class="mb-6" x-data="{ checked: {{ old('is_featured') ? 'true' : 'false' }} }">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="hidden" name="is_featured" value="0">
                                <input type="checkbox" name="is_featured" value="1" x-model="checked" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-warmYellow-500 focus:ring-warmYellow-500">
                                <span class="text-sm text-gray-700 dark:text-gray-300">⭐ {{ __('Tandai sebagai kenangan unggulan') }}</span>
                            </label>
                        </div>

                        <!-- Submit -->
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                            <a href="{{ route('timeline.index', $child) }}" class="inline-flex items-center justify-center px-6 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium rounded-xl text-sm hover:bg-gray-200 dark:hover:bg-gray-600 transition min-h-[44px]">
                                {{ __('Batal') }}
                            </a>
                            <button type="submit" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-softPink-500 hover:bg-softPink-600 text-white font-medium rounded-xl text-sm shadow-soft transition-all duration-200 min-h-[44px]">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ __('Simpan Kenangan') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
