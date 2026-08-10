<x-guest-layout>
    {{-- Header --}}
    <div class="mb-6 text-center lg:text-left">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Verifikasi Email 📧</h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Terima kasih telah mendaftar! Sebelum memulai, silakan verifikasi alamat email Anda dengan mengklik tautan yang kami kirimkan.</p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 p-3 bg-mintGreen-50 dark:bg-mintGreen-950/30 border border-mintGreen-200 dark:border-mintGreen-800 rounded-xl text-sm text-mintGreen-600 dark:text-mintGreen-400">
            {{ __('Tautan verifikasi baru telah dikirim ke alamat email yang Anda berikan saat pendaftaran.') }}
        </div>
    @endif

    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <form method="POST" action="{{ route('verification.send') }}" x-data="{ loading: false }" @submit="loading = true">
            @csrf

            <div>
                <button type="submit" x-bind:disabled="loading" class="inline-flex items-center justify-center gap-2 px-4 py-2 min-h-[44px] bg-softPink-400 dark:bg-softPink-500 border border-transparent rounded-xl font-semibold text-sm text-white tracking-wide hover:bg-softPink-500 dark:hover:bg-softPink-400 focus:bg-softPink-500 dark:focus:bg-softPink-400 active:bg-softPink-600 dark:active:bg-softPink-300 focus:outline-none focus:ring-2 focus:ring-softPink-300 focus:ring-offset-2 dark:focus:ring-offset-gray-800 shadow-soft transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg x-show="loading" class="w-4 h-4 shrink-0 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span x-text="loading ? '{{ __('Mengirim...') }}' : '{{ __('Kirim Ulang Email Verifikasi') }}'"></span>
                </button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                {{ __('Keluar') }}
            </button>
        </form>
    </div>
</x-guest-layout>
