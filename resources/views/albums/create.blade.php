<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('albums.index', $child) }}" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                📁 {{ __('Tambah Album') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-soft sm:rounded-3xl">
                <div class="p-6 sm:p-8">
                    <x-child-selector :children="$children" :child="$child" :route-name="'albums.create'" />

                    <!-- Child Info -->
                    <div class="flex items-center gap-3 mb-6 p-4 bg-gradient-to-r from-lavender-50 to-softPink-50 rounded-2xl">
                        <div class="w-10 h-10 rounded-xl {{ $child->gender === 'female' ? 'bg-softPink-100' : 'bg-skyBlue-100' }} flex items-center justify-center text-lg">
                            {{ $child->gender === 'female' ? '👧' : '👦' }}
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">{{ __('Album untuk') }}</p>
                            <p class="font-semibold text-gray-800">{{ $child->nickname ?? $child->name }}</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('albums.store', $child) }}">
                        @csrf

                        <!-- Name -->
                        <div class="mb-5">
                            <x-input-label for="name" :value="__('Nama Album')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus placeholder="Contoh: Momen Keluarga" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div class="mb-5">
                            <x-input-label for="description" :value="__('Deskripsi (opsional)')" />
                            <textarea id="description" name="description" rows="3" class="mt-1 block w-full border-gray-300 focus:border-lavender-500 focus:ring-lavender-500 rounded-2xl shadow-soft" placeholder="Ceritakan tentang album ini...">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- Privacy -->
                        <div class="mb-5" x-data="{ checked: {{ old('is_private', 'true') === 'true' || old('is_private') === '1' ? 'true' : 'false' }} }">
                            <x-input-label :value="__('Privasi')" />
                            <div class="mt-2 space-y-3">
                                <label class="flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition" :class="checked === false ? 'border-mintGreen-400 bg-mintGreen-50' : 'border-gray-200 hover:border-gray-300'" @click="checked = false">
                                    <input type="radio" name="is_private" value="0" :checked="!checked" class="text-mintGreen-500 focus:ring-mintGreen-500">
                                    <div>
                                        <p class="text-sm font-medium text-gray-700">🌐 {{ __('Publik') }}</p>
                                        <p class="text-xs text-gray-500">{{ __('Dapat dilihat oleh semua orang') }}</p>
                                    </div>
                                </label>
                                <label class="flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition" :class="checked === true ? 'border-lavender-400 bg-lavender-50' : 'border-gray-200 hover:border-gray-300'" @click="checked = true">
                                    <input type="radio" name="is_private" value="1" :checked="checked" class="text-lavender-500 focus:ring-lavender-500">
                                    <div>
                                        <p class="text-sm font-medium text-gray-700">🔒 {{ __('Privat') }}</p>
                                        <p class="text-xs text-gray-500">{{ __('Hanya Anda yang dapat melihat') }}</p>
                                    </div>
                                </label>
                            </div>
                            <x-input-error :messages="$errors->get('is_private')" class="mt-2" />
                        </div>

                        <!-- Sort Order -->
                        <div class="mb-6">
                            <x-input-label for="sort_order" :value="__('Urutan')" />
                            <x-text-input id="sort_order" name="sort_order" type="number" class="mt-1 block w-full" :value="old('sort_order', 0)" min="0" />
                            <p class="mt-1 text-xs text-gray-400">{{ __('Urutan tampilan album (0 = paling atas)') }}</p>
                            <x-input-error :messages="$errors->get('sort_order')" class="mt-2" />
                        </div>

                        <!-- Submit -->
                        <div class="flex items-center gap-3">
                            <a href="{{ route('albums.index', $child) }}" class="btn-secondary">
                                {{ __('Batal') }}
                            </a>
                            <button type="submit" class="btn-primary">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ __('Simpan Album') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
