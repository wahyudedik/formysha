<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            ➕ {{ __('Tambah Paket Baru') }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-6">
            @include('super-admin.partials.sidebar')

            <div class="flex-1 min-w-0">
                <x-breadcrumb :items="[
                    ['label' => 'Dashboard', 'url' => route('super-admin.dashboard')],
                    ['label' => 'Paket', 'url' => route('super-admin.plans.index')],
                    ['label' => 'Tambah Baru'],
                ]" />

                <form method="POST" action="{{ route('super-admin.plans.store') }}">
                    @csrf

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        {{-- Basic Info --}}
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft overflow-hidden">
                            <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-gray-700">
                                <h3 class="font-semibold text-gray-800 dark:text-gray-100">📋 {{ __('Informasi Dasar') }}</h3>
                            </div>
                            <div class="p-4 sm:p-6 space-y-5">
                                <div>
                                    <x-input-label for="name" :value="__('Nama Paket')" />
                                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full input-focus" :value="old('name')" required autofocus />
                                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                                </div>

                                <div>
                                    <x-input-label for="slug" :value="__('Slug (opsional)')" />
                                    <x-text-input id="slug" name="slug" type="text" class="mt-1 block w-full input-focus" :value="old('slug')" placeholder="Otomatis dari nama jika kosong" />
                                    <x-input-error :messages="$errors->get('slug')" class="mt-1" />
                                </div>

                                <div>
                                    <x-input-label for="description" :value="__('Deskripsi')" />
                                    <textarea id="description" name="description" rows="3" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-xl focus:border-softPink-300 focus:ring-softPink-200 text-sm" placeholder="Deskripsi singkat paket ini...">{{ old('description') }}</textarea>
                                    <x-input-error :messages="$errors->get('description')" class="mt-1" />
                                </div>

                                <div class="flex items-center gap-3">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="hidden" name="is_active" value="0">
                                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }} class="w-5 h-5 rounded-lg border-gray-300 dark:border-gray-600 text-softPink-400 focus:ring-softPink-300">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Paket Aktif</span>
                                    </label>
                                </div>

                                <div>
                                    <x-input-label for="sort_order" :value="__('Urutan')" />
                                    <x-text-input id="sort_order" name="sort_order" type="number" class="mt-1 block w-full input-focus" :value="old('sort_order', 0)" min="0" />
                                </div>
                            </div>
                        </div>

                        {{-- Pricing --}}
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft overflow-hidden">
                            <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-gray-700">
                                <h3 class="font-semibold text-gray-800 dark:text-gray-100">💰 {{ __('Harga') }}</h3>
                            </div>
                            <div class="p-4 sm:p-6 space-y-5">
                                <div>
                                    <x-input-label for="price_monthly" :value="__('Harga per Bulan (Rp)')" />
                                    <x-text-input id="price_monthly" name="price_monthly" type="number" class="mt-1 block w-full input-focus" :value="old('price_monthly', 0)" min="0" required />
                                    <x-input-error :messages="$errors->get('price_monthly')" class="mt-1" />
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Masukkan 0 untuk paket gratis.</p>
                                </div>

                                <div>
                                    <x-input-label for="price_yearly" :value="__('Harga per Tahun (Rp)')" />
                                    <x-text-input id="price_yearly" name="price_yearly" type="number" class="mt-1 block w-full input-focus" :value="old('price_yearly', 0)" min="0" />
                                    <x-input-error :messages="$errors->get('price_yearly')" class="mt-1" />
                                </div>
                            </div>
                        </div>

                        {{-- Limits --}}
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft overflow-hidden">
                            <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-gray-700">
                                <h3 class="font-semibold text-gray-800 dark:text-gray-100">📊 {{ __('Batasan') }}</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Gunakan -1 untuk unlimited.</p>
                            </div>
                            <div class="p-4 sm:p-6 space-y-5">
                                <div>
                                    <x-input-label for="max_children" :value="__('Maks. Anak')" />
                                    <x-text-input id="max_children" name="max_children" type="number" class="mt-1 block w-full input-focus" :value="old('max_children', 1)" min="-1" required />
                                </div>

                                <div>
                                    <x-input-label for="max_photos" :value="__('Maks. Foto')" />
                                    <x-text-input id="max_photos" name="max_photos" type="number" class="mt-1 block w-full input-focus" :value="old('max_photos', 50)" min="-1" required />
                                </div>

                                <div>
                                    <x-input-label for="max_videos" :value="__('Maks. Video')" />
                                    <x-text-input id="max_videos" name="max_videos" type="number" class="mt-1 block w-full input-focus" :value="old('max_videos', 10)" min="-1" required />
                                </div>

                                <div>
                                    <x-input-label for="max_storage_mb" :value="__('Maks. Storage (MB)')" />
                                    <x-text-input id="max_storage_mb" name="max_storage_mb" type="number" class="mt-1 block w-full input-focus" :value="old('max_storage_mb', 500)" min="-1" required />
                                </div>

                                <div>
                                    <x-input-label for="max_family_members" :value="__('Maks. Anggota Keluarga')" />
                                    <x-text-input id="max_family_members" name="max_family_members" type="number" class="mt-1 block w-full input-focus" :value="old('max_family_members', 5)" min="-1" />
                                </div>

                                <div>
                                    <x-input-label for="max_export_per_day" :value="__('Maks. Export/Hari')" />
                                    <x-text-input id="max_export_per_day" name="max_export_per_day" type="number" class="mt-1 block w-full input-focus" :value="old('max_export_per_day', 3)" min="-1" required />
                                </div>
                            </div>
                        </div>

                        {{-- Features --}}
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft overflow-hidden">
                            <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-gray-700">
                                <h3 class="font-semibold text-gray-800 dark:text-gray-100">✨ {{ __('Fitur') }}</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Satu fitur per baris.</p>
                            </div>
                            <div class="p-4 sm:p-6">
                                <textarea name="features[]" rows="6" class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-xl focus:border-softPink-300 focus:ring-softPink-200 text-sm" placeholder="Timeline&#10;Galeri Foto&#10;Diary&#10;Dokumen&#10;Pertumbuhan&#10;Kesehatan&#10;Kalender&#10;Family Sharing">{{ is_array(old('features')) ? implode("\n", old('features')) : '' }}</textarea>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Setiap baris = satu fitur yang tersedia.</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                        <button type="submit" class="btn-primary text-sm min-h-[44px] inline-flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            {{ __('Simpan Paket') }}
                        </button>
                        <a href="{{ route('super-admin.plans.index') }}" class="btn-secondary text-sm min-h-[44px] inline-flex items-center">
                            {{ __('Batal') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const nameInput = document.getElementById('name');
            const slugInput = document.getElementById('slug');
            if (nameInput && slugInput) {
                nameInput.addEventListener('input', function() {
                    if (!slugInput.value || slugInput.dataset.auto === 'true') {
                        slugInput.value = this.value.toLowerCase()
                            .replace(/[^a-z0-9]+/g, '-')
                            .replace(/^-|-$/g, '');
                        slugInput.dataset.auto = 'true';
                    }
                });
                slugInput.addEventListener('input', function() {
                    this.dataset.auto = 'false';
                });
            }
        });
    </script>
    @endpush
</x-app-layout>
