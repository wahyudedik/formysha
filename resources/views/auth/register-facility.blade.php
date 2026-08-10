<x-guest-layout>
    {{-- Header --}}
    <div class="mb-6 text-center lg:text-left">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">🏢 Daftar Fasilitas Kesehatan</h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Daftarkan fasilitas kesehatan Anda di ForMysha</p>
    </div>

    <form method="POST" action="{{ route('register.facility') }}" x-data="{ loading: false, showPassword: false }" @submit="loading = true">
        @csrf

        {{-- Section: Data Admin --}}
        <div class="mb-6">
            <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider mb-3">👤 Data Admin</h3>

            <!-- Name -->
            <div>
                <x-input-label for="name" :value="__('Nama Lengkap')" />
                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Nama admin/penanggung jawab" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email -->
            <div class="mt-4">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="admin@fasilitas.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>
        </div>

        {{-- Divider --}}
        <hr class="border-gray-200 dark:border-gray-700 my-6" />

        {{-- Section: Data Fasilitas --}}
        <div class="mb-6">
            <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider mb-3">🏥 Data Fasilitas</h3>

            <!-- Facility Name -->
            <div>
                <x-input-label for="facility_name" :value="__('Nama Fasilitas *')" />
                <x-text-input id="facility_name" class="block mt-1 w-full" type="text" name="facility_name" :value="old('facility_name')" required placeholder="Contoh: Klinik Sehat Bersama" />
                <x-input-error :messages="$errors->get('facility_name')" class="mt-2" />
            </div>

            <!-- Facility Type -->
            <div class="mt-4">
                <x-input-label for="facility_type" :value="__('Tipe Fasilitas *')" />
                <select id="facility_type" name="facility_type" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-softPink-300 focus:ring-softPink-200 rounded-xl shadow-sm transition" required>
                    <option value="">{{ __('Pilih Tipe Fasilitas') }}</option>
                    @foreach ($facilityTypes as $type)
                        <option value="{{ $type->value }}" {{ old('facility_type') == $type->value ? 'selected' : '' }}>{{ $type->label() }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('facility_type')" class="mt-2" />
            </div>

            <!-- Address -->
            <div class="mt-4">
                <x-input-label for="address" :value="__('Alamat')" />
                <textarea id="address" name="address" rows="2" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-softPink-300 focus:ring-softPink-200 rounded-xl shadow-sm transition" placeholder="Alamat lengkap fasilitas">{{ old('address') }}</textarea>
                <x-input-error :messages="$errors->get('address')" class="mt-2" />
            </div>

            <!-- Phone -->
            <div class="mt-4">
                <x-input-label for="phone" :value="__('Telepon')" />
                <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" placeholder="021-12345678" />
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>

            <!-- License Number -->
            <div class="mt-4">
                <x-input-label for="license_number" :value="__('Nomor Izin')" />
                <x-text-input id="license_number" class="block mt-1 w-full" type="text" name="license_number" :value="old('license_number')" placeholder="Nomor izin praktek/operasional" />
                <x-input-error :messages="$errors->get('license_number')" class="mt-2" />
            </div>

            <!-- Description -->
            <div class="mt-4">
                <x-input-label for="description" :value="__('Deskripsi')" />
                <textarea id="description" name="description" rows="2" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-softPink-300 focus:ring-softPink-200 rounded-xl shadow-sm transition" placeholder="Deskripsi singkat fasilitas">{{ old('description') }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>
        </div>

        <div class="mt-6">
            <button type="submit" x-bind:disabled="loading" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 min-h-[44px] bg-softPink-400 dark:bg-softPink-500 border border-transparent rounded-xl font-semibold text-sm text-white tracking-wide hover:bg-softPink-500 dark:hover:bg-softPink-400 focus:bg-softPink-500 dark:focus:bg-softPink-400 active:bg-softPink-600 dark:active:bg-softPink-300 focus:outline-none focus:ring-2 focus:ring-softPink-300 focus:ring-offset-2 dark:focus:ring-offset-gray-800 shadow-soft transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                <svg x-show="loading" class="w-4 h-4 shrink-0 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span x-text="loading ? '{{ __('Mendaftar...') }}' : '{{ __('Daftar Fasilitas') }}'"></span>
            </button>
        </div>
    </form>

    {{-- Links --}}
    <div class="mt-6 text-center space-y-2">
        <p class="text-sm text-gray-500 dark:text-gray-400">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="font-semibold text-softPink-400 hover:text-softPink-500 dark:text-softPink-300 dark:hover:text-softPink-200 transition-colors">
                Masuk
            </a>
        </p>
        <p class="text-sm text-gray-500 dark:text-gray-400">
            Ingin daftar sebagai keluarga?
            <a href="{{ route('register') }}" class="font-semibold text-skyBlue-400 hover:text-skyBlue-500 dark:text-skyBlue-300 dark:hover:text-skyBlue-200 transition-colors">
                Daftar Keluarga
            </a>
        </p>
    </div>
</x-guest-layout>
