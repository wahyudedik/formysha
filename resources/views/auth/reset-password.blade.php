<x-guest-layout>
    {{-- Header --}}
    <div class="mb-6 text-center lg:text-left">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Reset Password 🔒</h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Masukkan password baru Anda di bawah ini.</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" x-data="{ loading: false }" @submit="loading = true">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" placeholder="nama@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password Baru')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
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
            <button type="submit" x-bind:disabled="loading" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 min-h-[44px] bg-softPink-400 dark:bg-softPink-500 border border-transparent rounded-xl font-semibold text-sm text-white tracking-wide hover:bg-softPink-500 dark:hover:bg-softPink-400 focus:bg-softPink-500 dark:focus:bg-softPink-400 active:bg-softPink-600 dark:active:bg-softPink-300 focus:outline-none focus:ring-2 focus:ring-softPink-300 focus:ring-offset-2 dark:focus:ring-offset-gray-800 shadow-soft transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                <svg x-show="loading" class="w-4 h-4 shrink-0 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span x-text="loading ? '{{ __('Menyimpan...') }}' : '{{ __('Reset Password') }}'"></span>
            </button>
        </div>
    </form>
</x-guest-layout>
