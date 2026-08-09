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
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    {{ __('Kirim Ulang Email Verifikasi') }}
                </x-primary-button>
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
