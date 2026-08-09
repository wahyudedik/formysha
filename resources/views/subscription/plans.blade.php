<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            💎 {{ __('Pilih Paket Langganan') }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="text-center mb-10">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100 mb-2">Pilih Paket Terbaik</h1>
            <p class="text-gray-500 dark:text-gray-400 max-w-lg mx-auto">Sesuaikan kebutuhan keluarga Anda dengan paket yang tepat. Mulai dari gratis hingga enterprise.</p>
        </div>

        {{-- Pricing Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach ($plans as $plan)
                @php
                    $isFree = $plan->price_monthly === 0;
                    $gradients = [
                        'from-gray-50 to-gray-100',
                        'from-skyBlue-50 to-skyBlue-100',
                        'from-softPink-50 to-lavender-100',
                        'from-lavender-50 to-softPink-100',
                    ];
                    $gradient = $gradients[$loop->index % count($gradients)];
                    $borderColors = [
                        'border-gray-200',
                        'border-skyBlue-200',
                        'border-softPink-200',
                        'border-lavender-200',
                    ];
                    $borderColor = $borderColors[$loop->index % count($borderColors)];
                    $buttonColors = [
                        'bg-gray-600 hover:bg-gray-700',
                        'bg-skyBlue-400 hover:bg-skyBlue-500',
                        'bg-softPink-400 hover:bg-softPink-500',
                        'bg-lavender-400 hover:bg-lavender-500',
                    ];
                    $buttonColor = $buttonColors[$loop->index % count($buttonColors)];
                @endphp

                <div class="relative bg-gradient-to-br {{ $gradient }} rounded-3xl border {{ $borderColor }} dark:border-gray-600 overflow-hidden transition-all duration-300 hover:shadow-soft-lg hover:-translate-y-1 dark:bg-gray-800">
                    {{-- Popular Badge (for Premium) --}}
                    @if ($plan->slug === 'premium')
                        <div class="absolute top-0 right-0">
                            <div class="bg-softPink-400 text-white text-[10px] font-bold px-3 py-1 rounded-bl-xl">
                                ⭐ POPULER
                            </div>
                        </div>
                    @endif

                    <div class="p-6">
                        {{-- Plan Name --}}
                        <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-1">{{ $plan->name }}</h3>
                        @if ($plan->description)
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">{{ $plan->description }}</p>
                        @else
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">&nbsp;</p>
                        @endif

                        {{-- Price --}}
                        <div class="mb-6">
                            @if ($isFree)
                                <div class="flex items-baseline gap-1">
                                    <span class="text-3xl font-bold text-gray-800 dark:text-gray-100">Gratis</span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Selamanya</p>
                            @else
                                <div class="flex items-baseline gap-1">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">Rp</span>
                                    <span class="text-3xl font-bold text-gray-800 dark:text-gray-100">{{ number_format($plan->price_monthly, 0, ',', '.') }}</span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">per bulan</p>
                                @if ($plan->price_yearly)
                                    <p class="text-xs text-mintGreen-600 font-medium mt-1">
                                        💡 Rp {{ number_format($plan->price_yearly, 0, ',', '.') }}/tahun (hemat {{ round((1 - $plan->price_yearly / ($plan->price_monthly * 12)) * 100) }}%)
                                    </p>
                                @endif
                            @endif
                        </div>

                        {{-- Limits --}}
                        <div class="space-y-2 mb-6">
                            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                                <span class="text-mintGreen-500">✓</span>
                                {{ $plan->max_children === -1 ? 'Anak unlimited' : $plan->max_children . ' Anak' }}
                            </div>
                            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                                <span class="text-mintGreen-500">✓</span>
                                {{ $plan->max_photos === -1 ? 'Foto unlimited' : $plan->max_photos . ' Foto' }}
                            </div>
                            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                                <span class="text-mintGreen-500">✓</span>
                                {{ $plan->max_videos === -1 ? 'Video unlimited' : $plan->max_videos . ' Video' }}
                            </div>
                            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                                <span class="text-mintGreen-500">✓</span>
                                {{ $plan->getStorageFormatted() }} Storage
                            </div>
                        </div>

                        {{-- Features --}}
                        @if ($plan->features && count($plan->features) > 0)
                            <div class="space-y-2 mb-6 pt-4 border-t border-gray-200/50 dark:border-gray-600/50">
                                @foreach ($plan->features as $feature)
                                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                                        <span class="text-lavender-400">✨</span>
                                        {{ $feature }}
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- CTA Button --}}
                        <form method="POST" action="{{ route('subscription.subscribe', $plan) }}">
                            @csrf
                            <button type="submit" class="w-full py-3 rounded-xl text-white font-semibold text-sm {{ $buttonColor }} transition-all duration-200 shadow-soft hover:shadow-soft-md">
                                @if ($isFree)
                                    Mulai Gratis
                                @else
                                    Pilih Paket Ini
                                @endif
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- FAQ Section --}}
        <div class="mt-16 max-w-2xl mx-auto">
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 text-center mb-6">❓ Pertanyaan Umum</h2>
            <div class="space-y-4">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-5" x-data="{ open: false }">
                    <button @click="open = !open" class="w-full flex items-center justify-between text-left">
                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-100">Bagaimana cara membayar?</span>
                        <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                        Setelah memilih paket, Anda akan diarahkan ke halaman pembayaran. Lakukan transfer bank ke rekening yang tercantum, lalu upload bukti transfer. Tim kami akan memverifikasi dalam 1×24 jam.
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-5" x-data="{ open: false }">
                    <button @click="open = !open" class="w-full flex items-center justify-between text-left">
                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-100">Bisa ganti paket kapan saja?</span>
                        <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                        Ya, Anda dapat upgrade atau downgrade paket kapan saja. Perubahan akan berlaku setelah periode billing saat ini berakhir.
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-5" x-data="{ open: false }">
                    <button @click="open = !open" class="w-full flex items-center justify-between text-left">
                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-100">Apakah data saya aman?</span>
                        <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                        Ya! ForMysha menggunakan enkripsi data dan backup otomatis harian. Data keluarga Anda adalah prioritas utama kami.
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
