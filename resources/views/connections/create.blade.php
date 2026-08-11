<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('connections.index', $child) }}" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                {{ __('Tambah Koneksi Baru') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl p-4 sm:p-6 lg:p-8">
                <div class="mb-6">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __('Menghubungkan') }} <span class="font-semibold text-gray-800 dark:text-gray-100">{{ $child->nickname ?? $child->name }}</span> {{ __('dengan fasilitas atau organisasi.') }}
                    </p>
                </div>

                <form method="POST" action="{{ route('connections.store', $child) }}" class="space-y-6" x-data="{ loading: false }" @submit="loading = true">
                    @csrf

                    <!-- Tenant (Organization) -->
                    <div>
                        <x-input-label for="tenant_id" :value="__('Fasilitas / Organisasi *')" />
                        <select id="tenant_id" name="tenant_id" class="mt-1 block w-full border-gray-300 focus:border-softPink-300 focus:ring-softPink-200 rounded-xl shadow-sm transition dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200" required>
                            <option value="">{{ __('Pilih Fasilitas') }}</option>
                            @foreach ($tenants as $tenant)
                                <option value="{{ $tenant->id }}" {{ old('tenant_id') == $tenant->id ? 'selected' : '' }}>
                                    {{ $tenant->name }}{{ $tenant->type ? ' (' . $tenant->type . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('tenant_id')" />
                    </div>

                    <!-- Permission Level -->
                    <div>
                        <x-input-label for="permission" :value="__('Level Akses *')" />
                        <p class="text-xs text-gray-400 dark:text-gray-500 mb-2">{{ __('Tentukan seberapa banyak akses organisasi ini terhadap data anak.') }}</p>
                        <select id="permission" name="permission" class="mt-1 block w-full border-gray-300 focus:border-softPink-300 focus:ring-softPink-200 rounded-xl shadow-sm transition dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200" required>
                            @foreach ($permissions as $permission)
                                <option value="{{ $permission->value }}" {{ old('permission', 'view') === $permission->value ? 'selected' : '' }}>
                                    {{ $permission->label() }} — {{ $permission->description() }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('permission')" />
                    </div>

                    <!-- Notes -->
                    <div>
                        <x-input-label for="notes" :value="__('Catatan')" />
                        <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full border-gray-300 focus:border-softPink-300 focus:ring-softPink-200 rounded-xl shadow-sm transition dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200" placeholder="{{ __('Catatan opsional tentang koneksi ini...') }}">{{ old('notes') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('notes')" />
                    </div>

                    <!-- Submit -->
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4 pt-4">
                        <button type="submit" :disabled="loading" class="btn-primary min-h-[44px] disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg x-show="loading" class="w-4 h-4 mr-1 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <svg x-show="!loading" class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span x-text="loading ? '{{ __('Menyimpan...') }}' : '{{ __('Buat Koneksi') }}'"></span>
                        </button>
                        <a href="{{ route('connections.index', $child) }}" class="btn-secondary min-h-[44px]">
                            {{ __('Batal') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
