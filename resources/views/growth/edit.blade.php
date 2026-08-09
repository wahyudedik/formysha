<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('growth.index', $child) }}" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                📏 {{ __('Edit Pengukuran') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl">
                <div class="p-6 sm:p-8">
                    <!-- Child Info -->
                    <div class="flex items-center gap-3 mb-6 p-4 bg-gradient-to-r from-mintGreen-50 to-skyBlue-50 dark:from-mintGreen-950/30 dark:via-gray-800 dark:to-skyBlue-950/30 rounded-2xl">
                        <div class="w-10 h-10 rounded-xl {{ $child->gender === 'female' ? 'bg-softPink-100 dark:bg-softPink-950/30' : 'bg-skyBlue-100 dark:bg-skyBlue-950/30' }} flex items-center justify-center text-lg">
                            {{ $child->gender === 'female' ? '👧' : '👦' }}
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Edit pengukuran untuk') }}</p>
                            <p class="font-semibold text-gray-800 dark:text-gray-100">{{ $child->nickname ?? $child->name }}</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('growth.update', [$child, $growth]) }}">
                        @csrf
                        @method('PUT')

                        <!-- Date -->
                        <div class="mb-5">
                            <x-input-label for="measured_at" :value="__('Tanggal Pengukuran')" />
                            <x-text-input id="measured_at" name="measured_at" type="date" class="mt-1 block w-full" :value="old('measured_at', $growth->measured_at->format('Y-m-d'))" required />
                            <x-input-error :messages="$errors->get('measured_at')" class="mt-2" />
                        </div>

                        <!-- Weight & Height -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                            <div>
                                <x-input-label for="weight_kg" :value="__('Berat Badan (kg)')" />
                                <x-text-input id="weight_kg" name="weight_kg" type="number" step="0.1" min="0.1" max="200" class="mt-1 block w-full" :value="old('weight_kg', $growth->weight_kg)" placeholder="Contoh: 12.5" />
                                <x-input-error :messages="$errors->get('weight_kg')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="height_cm" :value="__('Tinggi Badan (cm)')" />
                                <x-text-input id="height_cm" name="height_cm" type="number" step="0.1" min="1" max="250" class="mt-1 block w-full" :value="old('height_cm', $growth->height_cm)" placeholder="Contoh: 85.0" />
                                <x-input-error :messages="$errors->get('height_cm')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Head Circumference -->
                        <div class="mb-5">
                            <x-input-label for="head_circumference_cm" :value="__('Lingkar Kepala (cm) — Opsional')" />
                            <x-text-input id="head_circumference_cm" name="head_circumference_cm" type="number" step="0.1" min="1" max="100" class="mt-1 block w-full" :value="old('head_circumference_cm', $growth->head_circumference_cm)" placeholder="Contoh: 45.0" />
                            <x-input-error :messages="$errors->get('head_circumference_cm')" class="mt-2" />
                        </div>

                        <!-- Notes -->
                        <div class="mb-6">
                            <x-input-label for="notes" :value="__('Catatan (opsional)')" />
                            <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full border-gray-300 dark:border-gray-600 focus:border-mintGreen-500 focus:ring-mintGreen-500 rounded-2xl shadow-soft dark:bg-gray-700 dark:text-gray-200" placeholder="Catatan tambahan tentang pengukuran ini...">{{ old('notes', $growth->notes) }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>

                        <!-- Submit -->
                        <div class="flex items-center gap-3">
                            <a href="{{ route('growth.index', $child) }}" class="btn-secondary">
                                {{ __('Batal') }}
                            </a>
                            <button type="submit" class="btn-primary">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ __('Perbarui Pengukuran') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
