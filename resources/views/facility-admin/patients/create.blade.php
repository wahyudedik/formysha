<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <x-breadcrumb :items="[
                    ['url' => route('facility.dashboard'), 'label' => __('Dashboard')],
                    ['url' => route('facility.patients.index'), 'label' => __('Pasien')],
                    ['label' => __('Daftarkan Pasien')],
                ]" />
                <h2 class="mt-2 font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                    👶 {{ __('Daftarkan Pasien') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-6">
                @include('facility-admin.partials.sidebar')
                <div class="flex-1 min-w-0">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl p-4 sm:p-6 lg:p-8">
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Data Pasien') }}</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Pilih anak dan orang tua yang ingin dihubungkan.') }}</p>
                        </div>

                        @php
                            $childOptions = collect($children)->map(fn($child) => [
                                'value' => (string) $child->id,
                                'label' => $child->name,
                                'sublabel' => $child->date_of_birth?->format('d M Y') ?? '-',
                            ])->values()->all();

                            $parentOptions = collect($parents)->map(fn($parent) => [
                                'value' => (string) $parent->id,
                                'label' => $parent->name,
                                'sublabel' => $parent->email,
                            ])->values()->all();
                        @endphp

                        <form method="POST" action="{{ route('facility.patients.store') }}" class="space-y-6" x-data="{ loading: false }" @submit="loading = true">
                            @csrf

                            <!-- Child (Searchable) -->
                            <x-searchable-select
                                name="child_id"
                                label="{{ __('Anak (Pasien)') }}"
                                :required="true"
                                :options="$childOptions"
                                selected="{{ old('child_id') }}"
                                placeholder="{{ __('Ketik nama anak untuk mencari...') }}"
                                error="{{ $errors->first('child_id') }}"
                            />

                            <!-- Parent User (Searchable) -->
                            <x-searchable-select
                                name="parent_user_id"
                                label="{{ __('Orang Tua') }}"
                                :required="true"
                                :options="$parentOptions"
                                selected="{{ old('parent_user_id') }}"
                                placeholder="{{ __('Ketik nama atau email orang tua...') }}"
                                error="{{ $errors->first('parent_user_id') }}"
                            />

                            <!-- Permissions -->
                            <div>
                                <x-input-label :value="__('Izin Akses')" />
                                <div class="mt-2 space-y-2">
                                    @php
                                        $permissionOptions = [
                                            'view_timeline' => 'Lihat Timeline',
                                            'view_growth' => 'Lihat Pertumbuhan',
                                            'view_health' => 'Lihat Kesehatan',
                                            'view_documents' => 'Lihat Dokumen',
                                            'view_gallery' => 'Lihat Galeri',
                                        ];
                                        $currentPermissions = old('permissions', ['view_timeline', 'view_growth', 'view_health']);
                                    @endphp
                                    @foreach ($permissionOptions as $key => $label)
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" name="permissions[]" value="{{ $key }}" {{ in_array($key, $currentPermissions) ? 'checked' : '' }} class="rounded border-gray-300 text-softPink-600 shadow-sm focus:ring-softPink-500" />
                                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <x-input-error class="mt-2" :messages="$errors->get('permissions')" />
                            </div>

                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                                <button type="submit" x-bind:disabled="loading" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-softPink-500 hover:bg-softPink-600 text-white font-semibold rounded-xl shadow-soft transition-all duration-200 min-h-[44px] disabled:opacity-50 disabled:cursor-not-allowed">
                                    <svg x-show="loading" class="w-4 h-4 shrink-0 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    <span x-text="loading ? '{{ __('Menyimpan...') }}' : '{{ __('Simpan & Buat Kode') }}'"></span>
                                </button>
                                <a href="{{ route('facility.patients.index') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-xl transition min-h-[44px]">
                                    {{ __('Batal') }}
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
