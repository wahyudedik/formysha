<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('family.index', $child) }}" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                {{ __('Tambah Anggota Keluarga') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl p-4 sm:p-6 lg:p-8">
                <x-child-selector :children="$children" :child="$child" :route-name="'family.create'" />

                <div class="mb-6">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __('Menambahkan anggota keluarga untuk') }} <span class="font-semibold text-gray-800 dark:text-gray-100">{{ $child->nickname ?? $child->name }}</span>
                    </p>
                </div>

                <form method="POST" action="{{ route('family.store', $child) }}" class="space-y-6" x-data="{ loading: false }" @submit="loading = true">
                    @csrf

                    <!-- Name -->
                    <div>
                        <x-input-label for="name" :value="__('Nama Lengkap *')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus placeholder="Contoh: Budi Santoso" />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <!-- Relationship -->
                    <div>
                        <x-input-label for="relationship" :value="__('Hubungan *')" />
                        <select id="relationship" name="relationship" class="mt-1 block w-full border-gray-300 focus:border-softPink-300 focus:ring-softPink-200 rounded-xl shadow-sm transition dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200" required>
                            <option value="">{{ __('Pilih Hubungan') }}</option>
                            <option value="father" {{ old('relationship') === 'father' ? 'selected' : '' }}>👨 Ayah</option>
                            <option value="mother" {{ old('relationship') === 'mother' ? 'selected' : '' }}>👩 Ibu</option>
                            <option value="guardian" {{ old('relationship') === 'guardian' ? 'selected' : '' }}>🤝 Wali</option>
                            <option value="grandfather" {{ old('relationship') === 'grandfather' ? 'selected' : '' }}>👴 Kakek</option>
                            <option value="grandmother" {{ old('relationship') === 'grandmother' ? 'selected' : '' }}>👵 Nenek</option>
                            <option value="sibling" {{ old('relationship') === 'sibling' ? 'selected' : '' }}>🧒 Saudara/i</option>
                            <option value="other" {{ old('relationship') === 'other' ? 'selected' : '' }}>👤 Lainnya</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('relationship')" />
                    </div>

                    <!-- Phone -->
                    <div>
                        <x-input-label for="phone" :value="__('Nomor Telepon')" />
                        <x-text-input id="phone" name="phone" type="tel" class="mt-1 block w-full" :value="old('phone')" placeholder="Contoh: 08123456789" />
                        <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                    </div>

                    <!-- Email -->
                    <div>
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" placeholder="Contoh: budi@email.com" />
                        <x-input-error class="mt-2" :messages="$errors->get('email')" />
                    </div>

                    <!-- Primary Toggle -->
                    <div class="flex items-center gap-3">
                        <input type="hidden" name="is_primary" value="0">
                        <input type="checkbox" id="is_primary" name="is_primary" value="1" {{ old('is_primary') ? 'checked' : '' }} class="rounded border-gray-300 text-softPink-300 focus:ring-softPink-200 dark:border-gray-600 dark:bg-gray-700">
                        <x-input-label for="is_primary" :value="__('Tandai sebagai kontak utama')" class="!mb-0" />
                    </div>

                    <!-- Submit -->
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4 pt-4">
                        <button type="submit" :disabled="loading" class="btn-primary min-h-[44px] disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg x-show="loading" class="w-4 h-4 mr-1 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <svg x-show="!loading" class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span x-text="loading ? '{{ __('Menyimpan...') }}' : '{{ __('Simpan') }}'"></span>
                        </button>
                        <a href="{{ route('family.index', $child) }}" class="btn-secondary min-h-[44px]">
                            {{ __('Batal') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
