<x-guest-layout>
    {{-- Header --}}
    <div class="mb-6 text-center lg:text-left">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Buat Akun Baru 💕</h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Mulai simpan kenangan berharga anak Anda</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Nama Lengkap')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Nama Anda" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="nama@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password"
                            placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password"
                            placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-6">
            <x-primary-button class="w-full justify-center">
                {{ __('Daftar') }}
            </x-primary-button>
        </div>
    </form>

    {{-- Login Link --}}
    <div class="mt-6 text-center">
        <p class="text-sm text-gray-500 dark:text-gray-400">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="font-semibold text-softPink-400 hover:text-softPink-500 dark:text-softPink-300 dark:hover:text-softPink-200 transition-colors">
                Masuk
            </a>
        </p>
    </div>
</x-guest-layout>
