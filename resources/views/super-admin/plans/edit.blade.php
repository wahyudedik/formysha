<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            ✏️ {{ __('Edit Paket') }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-6">
            @include('super-admin.partials.sidebar')

            <div class="flex-1 min-w-0">
                <x-breadcrumb :items="[
                    ['label' => 'Dashboard', 'url' => route('super-admin.dashboard')],
                    ['label' => 'Paket', 'url' => route('super-admin.plans.index')],
                    ['label' => $plan->name, 'url' => route('super-admin.plans.edit', $plan)],
                    ['label' => 'Edit'],
                ]" />

                <form method="POST" action="{{ route('super-admin.plans.update', $plan) }}">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        {{-- Basic Info --}}
                        <div class="bg-white rounded-2xl shadow-soft overflow-hidden">
                            <div class="p-6 border-b border-gray-100">
                                <h3 class="font-semibold text-gray-800">📋 {{ __('Informasi Dasar') }}</h3>
                            </div>
                            <div class="p-6 space-y-5">
                                <div>
                                    <x-input-label for="name" :value="__('Nama Paket')" />
                                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full input-focus" :value="old('name', $plan->name)" required />
                                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                                </div>

                                <div>
                                    <x-input-label for="slug" :value="__('Slug')" />
                                    <x-text-input id="slug" name="slug" type="text" class="mt-1 block w-full input-focus" :value="old('slug', $plan->slug)" />
                                    <x-input-error :messages="$errors->get('slug')" class="mt-1" />
                                </div>

                                <div>
                                    <x-input-label for="description" :value="__('Deskripsi')" />
                                    <textarea id="description" name="description" rows="3" class="mt-1 block w-full border-gray-300 rounded-xl focus:border-softPink-300 focus:ring-softPink-200 text-sm">{{ old('description', $plan->description) }}</textarea>
                                    <x-input-error :messages="$errors->get('description')" class="mt-1" />
                                </div>

                                <div class="flex items-center gap-3">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="hidden" name="is_active" value="0">
                                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $plan->is_active) ? 'checked' : '' }} class="w-5 h-5 rounded-lg border-gray-300 text-softPink-400 focus:ring-softPink-300">
                                        <span class="text-sm font-medium text-gray-700">Paket Aktif</span>
                                    </label>
                                </div>

                                <div>
                                    <x-input-label for="sort_order" :value="__('Urutan')" />
                                    <x-text-input id="sort_order" name="sort_order" type="number" class="mt-1 block w-full input-focus" :value="old('sort_order', $plan->sort_order)" min="0" />
                                </div>
                            </div>
                        </div>

                        {{-- Pricing --}}
                        <div class="bg-white rounded-2xl shadow-soft overflow-hidden">
                            <div class="p-6 border-b border-gray-100">
                                <h3 class="font-semibold text-gray-800">💰 {{ __('Harga') }}</h3>
                            </div>
                            <div class="p-6 space-y-5">
                                <div>
                                    <x-input-label for="price_monthly" :value="__('Harga per Bulan (Rp)')" />
                                    <x-text-input id="price_monthly" name="price_monthly" type="number" class="mt-1 block w-full input-focus" :value="old('price_monthly', $plan->price_monthly)" min="0" required />
                                    <x-input-error :messages="$errors->get('price_monthly')" class="mt-1" />
                                </div>

                                <div>
                                    <x-input-label for="price_yearly" :value="__('Harga per Tahun (Rp)')" />
                                    <x-text-input id="price_yearly" name="price_yearly" type="number" class="mt-1 block w-full input-focus" :value="old('price_yearly', $plan->price_yearly)" min="0" />
                                    <x-input-error :messages="$errors->get('price_yearly')" class="mt-1" />
                                </div>
                            </div>
                        </div>

                        {{-- Limits --}}
                        <div class="bg-white rounded-2xl shadow-soft overflow-hidden">
                            <div class="p-6 border-b border-gray-100">
                                <h3 class="font-semibold text-gray-800">📊 {{ __('Batasan') }}</h3>
                                <p class="text-xs text-gray-500 mt-1">Gunakan -1 untuk unlimited.</p>
                            </div>
                            <div class="p-6 space-y-5">
                                <div>
                                    <x-input-label for="max_children" :value="__('Maks. Anak')" />
                                    <x-text-input id="max_children" name="max_children" type="number" class="mt-1 block w-full input-focus" :value="old('max_children', $plan->max_children)" min="-1" required />
                                </div>

                                <div>
                                    <x-input-label for="max_photos" :value="__('Maks. Foto')" />
                                    <x-text-input id="max_photos" name="max_photos" type="number" class="mt-1 block w-full input-focus" :value="old('max_photos', $plan->max_photos)" min="-1" required />
                                </div>

                                <div>
                                    <x-input-label for="max_videos" :value="__('Maks. Video')" />
                                    <x-text-input id="max_videos" name="max_videos" type="number" class="mt-1 block w-full input-focus" :value="old('max_videos', $plan->max_videos)" min="-1" required />
                                </div>

                                <div>
                                    <x-input-label for="max_storage_mb" :value="__('Maks. Storage (MB)')" />
                                    <x-text-input id="max_storage_mb" name="max_storage_mb" type="number" class="mt-1 block w-full input-focus" :value="old('max_storage_mb', $plan->max_storage_mb)" min="-1" required />
                                </div>

                                <div>
                                    <x-input-label for="max_family_members" :value="__('Maks. Anggota Keluarga')" />
                                    <x-text-input id="max_family_members" name="max_family_members" type="number" class="mt-1 block w-full input-focus" :value="old('max_family_members', $plan->max_family_members)" min="-1" />
                                </div>

                                <div>
                                    <x-input-label for="max_export_per_day" :value="__('Maks. Export/Hari')" />
                                    <x-text-input id="max_export_per_day" name="max_export_per_day" type="number" class="mt-1 block w-full input-focus" :value="old('max_export_per_day', $plan->max_export_per_day)" min="-1" required />
                                </div>
                            </div>
                        </div>

                        {{-- Features --}}
                        <div class="bg-white rounded-2xl shadow-soft overflow-hidden">
                            <div class="p-6 border-b border-gray-100">
                                <h3 class="font-semibold text-gray-800">✨ {{ __('Fitur') }}</h3>
                                <p class="text-xs text-gray-500 mt-1">Satu fitur per baris.</p>
                            </div>
                            <div class="p-6">
                                <textarea name="features[]" rows="6" class="block w-full border-gray-300 rounded-xl focus:border-softPink-300 focus:ring-softPink-200 text-sm" placeholder="Timeline&#10;Galeri Foto&#10;Diary&#10;Dokumen">{{ is_array(old('features')) ? implode("\n", old('features')) : (is_array($plan->features) ? implode("\n", $plan->features) : '') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center gap-3">
                        <button type="submit" class="btn-primary text-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            {{ __('Simpan Perubahan') }}
                        </button>
                        <a href="{{ route('super-admin.plans.index') }}" class="btn-secondary text-sm">
                            {{ __('Batal') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
