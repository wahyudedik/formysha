<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            ✏️ {{ __('Edit Tenant') }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-6">
            @include('super-admin.partials.sidebar')

            <div class="flex-1 min-w-0">
                <x-breadcrumb :items="[
                    ['label' => 'Dashboard', 'url' => route('super-admin.dashboard')],
                    ['label' => 'Tenants', 'url' => route('super-admin.tenants.index')],
                    ['label' => $tenant->name, 'url' => route('super-admin.tenants.show', $tenant)],
                    ['label' => 'Edit'],
                ]" />

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft overflow-hidden">
                    <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-100">Edit: {{ $tenant->name }}</h3>
                    </div>

                    <form method="POST" action="{{ route('super-admin.tenants.update', $tenant) }}" class="p-4 sm:p-6">
                        @csrf
                        @method('PUT')

                        <div class="max-w-lg space-y-5">
                            {{-- Name --}}
                            <div>
                                <x-input-label for="name" :value="__('Nama Tenant')" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full input-focus" :value="old('name', $tenant->name)" required />
                                <x-input-error :messages="$errors->get('name')" class="mt-1" />
                            </div>

                            {{-- Slug --}}
                            <div>
                                <x-input-label for="slug" :value="__('Slug')" />
                                <x-text-input id="slug" name="slug" type="text" class="mt-1 block w-full input-focus" :value="old('slug', $tenant->slug)" />
                                <x-input-error :messages="$errors->get('slug')" class="mt-1" />
                            </div>

                            {{-- Is Active --}}
                            <div>
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="hidden" name="is_active" value="0">
                                    <input
                                        type="checkbox"
                                        name="is_active"
                                        value="1"
                                        {{ old('is_active', $tenant->is_active) ? 'checked' : '' }}
                                        class="w-5 h-5 rounded-lg border-gray-300 dark:border-gray-600 text-softPink-400 focus:ring-softPink-300"
                                    >
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Tenant Aktif</span>
                                </label>
                            </div>
                        </div>

                        <div class="mt-6 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                            <button type="submit" class="btn-primary text-sm min-h-[44px] inline-flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ __('Simpan') }}
                            </button>
                            <a href="{{ route('super-admin.tenants.show', $tenant) }}" class="btn-secondary text-sm min-h-[44px] inline-flex items-center">
                                {{ __('Batal') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
