<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('albums.show', [$child, $album]) }}" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                ✏️ {{ __('Edit Album') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl">
                <div class="p-4 sm:p-6 lg:p-8">
                    <!-- Child Info -->
                    <div class="flex items-center gap-3 mb-6 p-4 bg-gradient-to-r from-lavender-50 to-softPink-50 dark:from-lavender-950/30 dark:to-softPink-950/30 rounded-2xl">
                        <div class="w-10 h-10 rounded-xl {{ $child->gender === 'female' ? 'bg-softPink-100 dark:bg-softPink-950/30' : 'bg-skyBlue-100 dark:bg-skyBlue-950/30' }} flex items-center justify-center text-lg">
                            {{ $child->gender === 'female' ? '👧' : '👦' }}
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Edit album untuk') }}</p>
                            <p class="font-semibold text-gray-800 dark:text-gray-100">{{ $child->nickname ?? $child->name }}</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('albums.update', [$child, $album]) }}">
                        @csrf
                        @method('PUT')

                        <!-- Name -->
                        <div class="mb-5">
                            <x-input-label for="name" :value="__('Nama Album')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $album->name)" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div class="mb-5">
                            <x-input-label for="description" :value="__('Deskripsi (opsional)')" />
                            <textarea id="description" name="description" rows="3" class="mt-1 block w-full border-gray-300 focus:border-lavender-500 focus:ring-lavender-500 rounded-2xl shadow-soft dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">{{ old('description', $album->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- Privacy -->
                        <div class="mb-5" x-data="{ checked: {{ old('is_private', $album->is_private ? 'true' : 'false') }} }">
                            <x-input-label :value="__('Privasi')" />
                            <div class="mt-2 space-y-3">
                                <label class="flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition" :class="checked === false ? 'border-mintGreen-400 bg-mintGreen-50 dark:bg-mintGreen-950/30' : 'border-gray-200 hover:border-gray-300 dark:border-gray-600 dark:hover:border-gray-500'" @click="checked = false">
                                    <input type="radio" name="is_private" value="0" :checked="!checked" class="text-mintGreen-500 focus:ring-mintGreen-500">
                                    <div>
                                        <p class="text-sm font-medium text-gray-700 dark:text-gray-200">🌐 {{ __('Publik') }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Dapat dilihat oleh semua orang') }}</p>
                                    </div>
                                </label>
                                <label class="flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition" :class="checked === true ? 'border-lavender-400 bg-lavender-50 dark:bg-lavender-950/30' : 'border-gray-200 hover:border-gray-300 dark:border-gray-600 dark:hover:border-gray-500'" @click="checked = true">
                                    <input type="radio" name="is_private" value="1" :checked="checked" class="text-lavender-500 focus:ring-lavender-500">
                                    <div>
                                        <p class="text-sm font-medium text-gray-700 dark:text-gray-200">🔒 {{ __('Privat') }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Hanya Anda yang dapat melihat') }}</p>
                                    </div>
                                </label>
                            </div>
                            <x-input-error :messages="$errors->get('is_private')" class="mt-2" />
                        </div>

                        <!-- Sort Order -->
                        <div class="mb-6">
                            <x-input-label for="sort_order" :value="__('Urutan')" />
                            <x-text-input id="sort_order" name="sort_order" type="number" class="mt-1 block w-full" :value="old('sort_order', $album->sort_order)" min="0" />
                            <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">{{ __('Urutan tampilan album (0 = paling atas)') }}</p>
                            <x-input-error :messages="$errors->get('sort_order')" class="mt-2" />
                        </div>

                        <!-- Submit -->
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                            <a href="{{ route('albums.show', [$child, $album]) }}" class="inline-flex items-center justify-center px-6 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium rounded-xl text-sm hover:bg-gray-200 dark:hover:bg-gray-600 transition min-h-[44px]">
                                {{ __('Batal') }}
                            </a>
                            <button type="submit" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-softPink-500 hover:bg-softPink-600 text-white font-medium rounded-xl text-sm shadow-soft transition-all duration-200 min-h-[44px]">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ __('Perbarui Album') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
