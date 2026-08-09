<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            ➕ {{ __('Tambah Tenant Baru') }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-6">
            @include('super-admin.partials.sidebar')

            <div class="flex-1 min-w-0">
                <x-breadcrumb :items="[
                    ['label' => 'Dashboard', 'url' => route('super-admin.dashboard')],
                    ['label' => 'Tenants', 'url' => route('super-admin.tenants.index')],
                    ['label' => 'Tambah Baru'],
                ]" />

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft overflow-hidden">
                    <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-100">Form Tambah Tenant</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Isi informasi tenant baru di bawah ini.</p>
                    </div>

                    <form method="POST" action="{{ route('super-admin.tenants.store') }}" class="p-4 sm:p-6" x-data="{ loading: false }" @submit="loading = true">
                        @csrf

                        <div class="max-w-lg space-y-5">
                            {{-- Name --}}
                            <div>
                                <x-input-label for="name" :value="__('Nama Tenant')" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full input-focus" :value="old('name')" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-1" />
                            </div>

                            {{-- Slug --}}
                            <div>
                                <x-input-label for="slug" :value="__('Slug (opsional)')" />
                                <x-text-input id="slug" name="slug" type="text" class="mt-1 block w-full input-focus" :value="old('slug')" placeholder="Otomatis dari nama jika kosong" />
                                <x-input-error :messages="$errors->get('slug')" class="mt-1" />
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Jika dikosongkan, slug akan dibuat otomatis dari nama.</p>
                            </div>
                        </div>

                        <div class="mt-6 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                            <button type="submit" :disabled="loading" class="btn-primary text-sm min-h-[44px] inline-flex items-center disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg x-show="loading" class="w-4 h-4 mr-1 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <svg x-show="!loading" class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span x-text="loading ? '{{ __('Menyimpan...') }}' : '{{ __('Simpan') }}'"></span>
                            </button>
                            <a href="{{ route('super-admin.tenants.index') }}" class="btn-secondary text-sm min-h-[44px] inline-flex items-center">
                                {{ __('Batal') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

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
</x-app-layout>
