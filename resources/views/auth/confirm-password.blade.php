<x-guest-layout>
    {{-- Header --}}
    <div class="mb-6 text-center lg:text-left">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Konfirmasi Password 🔐</h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Ini adalah area aman dari aplikasi. Silakan konfirmasi password Anda sebelum melanjutkan.</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password"
                            placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-6">
            <x-primary-button class="w-full justify-center">
                {{ __('Konfirmasi') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
