<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            💎 {{ __('Pilih Paket Langganan') }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="text-center mb-10">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100 mb-2">{{ __('Pilih Paket Terbaik') }}</h1>
            <p class="text-gray-500 dark:text-gray-400 max-w-lg mx-auto">{{ __('Sesuaikan kebutuhan keluarga Anda dengan paket yang tepat. Mulai dari gratis hingga enterprise.') }}</p>
        </div>

        {{-- Pricing Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach ($plans as $plan)
                @php
                    $isFree = $plan->price_monthly === 0;
                    $gradients = [
                        'from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-750',
                        'from-skyBlue-50 to-skyBlue-100 dark:from-skyBlue-950/30 dark:to-skyBlue-900/30',
                        'from-softPink-50 to-lavender-100 dark:from-softPink-950/30 dark:to-lavender-950/30',
                        'from-lavender-50 to-softPink-100 dark:from-lavender-950/30 dark:to-softPink-950/30',
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
                                ⭐ {{ __('POPULER') }}
                            </div>
                        </div>
                    @endif

                    <div class="p-4 sm:p-6">
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
                                    <span class="text-3xl font-bold text-gray-800 dark:text-gray-100">{{ __('Gratis') }}</span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Selamanya') }}</p>
                            @else
                                <div class="flex items-baseline gap-1">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">Rp</span>
                                    <span class="text-3xl font-bold text-gray-800 dark:text-gray-100">{{ number_format($plan->price_monthly, 0, ',', '.') }}</span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('per bulan') }}</p>
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
                                {{ $plan->max_children === -1 ? __('Anak unlimited') : $plan->max_children . ' ' . __('Anak') }}
                            </div>
                            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                                <span class="text-mintGreen-500">✓</span>
                                {{ $plan->max_photos === -1 ? __('Foto unlimited') : $plan->max_photos . ' ' . __('Foto') }}
                            </div>
                            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                                <span class="text-mintGreen-500">✓</span>
                                {{ $plan->max_videos === -1 ? __('Video unlimited') : $plan->max_videos . ' ' . __('Video') }}
                            </div>
                            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                                <span class="text-mintGreen-500">✓</span>
                                {{ $plan->getStorageFormatted() }} {{ __('Storage') }}
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
                        <form method="POST" action="{{ route('subscription.subscribe', $plan) }}" x-data="{ loading: false }" @submit="loading = true">
                            @csrf
                            <button type="submit" x-bind:disabled="loading" class="w-full py-3 min-h-[44px] rounded-xl text-white font-semibold text-sm {{ $buttonColor }} transition-all duration-200 shadow-soft hover:shadow-soft-md disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center justify-center gap-2">
                                <svg x-show="loading" class="w-4 h-4 shrink-0 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                @if ($isFree)
                                    <span x-text="loading ? '{{ __('Mengaktifkan...') }}' : '{{ __('Mulai Gratis') }}'"></span>
                                @else
                                    <span x-text="loading ? '{{ __('Memproses...') }}' : '{{ __('Pilih Paket Ini') }}'"></span>
                                @endif
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Feature Comparison Table --}}
        <div class="mt-16">
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 text-center mb-6">📊 {{ __('Perbandingan Fitur') }}</h2>
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-soft overflow-hidden">
                {{-- Desktop Table --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-700">
                                <th class="text-left py-4 px-6 text-sm font-semibold text-gray-500 dark:text-gray-400">{{ __('Fitur') }}</th>
                                @foreach ($plans as $plan)
                                    <th class="text-center py-4 px-4">
                                        <span class="text-sm font-bold text-gray-800 dark:text-gray-100">{{ $plan->name }}</span>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                            {{-- Harga --}}
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="py-3 px-6 text-sm text-gray-600 dark:text-gray-300">{{ __('Harga/Bulan') }}</td>
                                @foreach ($plans as $plan)
                                    <td class="py-3 px-4 text-center">
                                        @if ($plan->price_monthly === 0)
                                            <span class="text-sm font-semibold text-mintGreen-600">{{ __('Gratis') }}</span>
                                        @else
                                            <span class="text-sm font-semibold text-gray-800 dark:text-gray-100">Rp {{ number_format($plan->price_monthly, 0, ',', '.') }}</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                            {{-- Anak --}}
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="py-3 px-6 text-sm text-gray-600 dark:text-gray-300">{{ __('Anak') }}</td>
                                @foreach ($plans as $plan)
                                    <td class="py-3 px-4 text-center">
                                        @if ($plan->max_children === -1)
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-mintGreen-100 dark:bg-mintGreen-900/30">
                                                <svg class="w-4 h-4 text-mintGreen-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                            </span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">{{ __('Unlimited') }}</span>
                                        @else
                                            <span class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $plan->max_children }}</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                            {{-- Foto --}}
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="py-3 px-6 text-sm text-gray-600 dark:text-gray-300">{{ __('Foto') }}</td>
                                @foreach ($plans as $plan)
                                    <td class="py-3 px-4 text-center">
                                        @if ($plan->max_photos === -1)
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-mintGreen-100 dark:bg-mintGreen-900/30">
                                                <svg class="w-4 h-4 text-mintGreen-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                            </span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">{{ __('Unlimited') }}</span>
                                        @else
                                            <span class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ number_format($plan->max_photos) }}</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                            {{-- Video --}}
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="py-3 px-6 text-sm text-gray-600 dark:text-gray-300">{{ __('Video') }}</td>
                                @foreach ($plans as $plan)
                                    <td class="py-3 px-4 text-center">
                                        @if ($plan->max_videos === -1)
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-mintGreen-100 dark:bg-mintGreen-900/30">
                                                <svg class="w-4 h-4 text-mintGreen-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                            </span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">{{ __('Unlimited') }}</span>
                                        @else
                                            <span class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ number_format($plan->max_videos) }}</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                            {{-- Storage --}}
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="py-3 px-6 text-sm text-gray-600 dark:text-gray-300">{{ __('Penyimpanan') }}</td>
                                @foreach ($plans as $plan)
                                    <td class="py-3 px-4 text-center">
                                        <span class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $plan->getStorageFormatted() }}</span>
                                    </td>
                                @endforeach
                            </tr>
                            {{-- Anggota Keluarga --}}
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="py-3 px-6 text-sm text-gray-600 dark:text-gray-300">{{ __('Anggota Keluarga') }}</td>
                                @foreach ($plans as $plan)
                                    <td class="py-3 px-4 text-center">
                                        @if ($plan->max_family_members === -1)
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-mintGreen-100 dark:bg-mintGreen-900/30">
                                                <svg class="w-4 h-4 text-mintGreen-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                            </span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">{{ __('Unlimited') }}</span>
                                        @else
                                            <span class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $plan->max_family_members }}</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                            {{-- Export per Hari --}}
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="py-3 px-6 text-sm text-gray-600 dark:text-gray-300">{{ __('Export/Hari') }}</td>
                                @foreach ($plans as $plan)
                                    <td class="py-3 px-4 text-center">
                                        @if ($plan->max_export_per_day === -1)
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-mintGreen-100 dark:bg-mintGreen-900/30">
                                                <svg class="w-4 h-4 text-mintGreen-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                            </span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">{{ __('Unlimited') }}</span>
                                        @else
                                            <span class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $plan->max_export_per_day }}</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                            {{-- Fitur --}}
                            @php
                                $allFeatures = collect($plans)->flatMap(fn ($p) => $p->features ?? [])->unique()->values()->all();
                            @endphp
                            @foreach ($allFeatures as $feature)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                                    <td class="py-3 px-6 text-sm text-gray-600 dark:text-gray-300">{{ $feature }}</td>
                                    @foreach ($plans as $plan)
                                        <td class="py-3 px-4 text-center">
                                            @if (in_array($feature, $plan->features ?? []))
                                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-mintGreen-100 dark:bg-mintGreen-900/30">
                                                    <svg class="w-4 h-4 text-mintGreen-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                                </span>
                                            @else
                                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gray-100 dark:bg-gray-700">
                                                    <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                                </span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Cards --}}
                <div class="md:hidden divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach ($plans as $plan)
                        @php
                            $cardBorderColors = [
                                'border-gray-200 dark:border-gray-600',
                                'border-skyBlue-200 dark:border-skyBlue-700',
                                'border-softPink-200 dark:border-softPink-700',
                                'border-lavender-200 dark:border-lavender-700',
                            ];
                            $cardBorderColor = $cardBorderColors[$loop->index % count($cardBorderColors)];
                        @endphp
                        <div class="p-4 {{ $cardBorderColor }}">
                            <h3 class="text-sm font-bold text-gray-800 dark:text-gray-100 mb-3">{{ $plan->name }}</h3>
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('Harga/Bulan') }}</span>
                                    @if ($plan->price_monthly === 0)
                                        <span class="text-xs font-semibold text-mintGreen-600">{{ __('Gratis') }}</span>
                                    @else
                                        <span class="text-xs font-semibold text-gray-800 dark:text-gray-100">Rp {{ number_format($plan->price_monthly, 0, ',', '.') }}</span>
                                    @endif
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('Anak') }}</span>
                                    <span class="text-xs font-medium text-gray-800 dark:text-gray-100">{{ $plan->max_children === -1 ? __('Unlimited') : $plan->max_children }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('Foto') }}</span>
                                    <span class="text-xs font-medium text-gray-800 dark:text-gray-100">{{ $plan->max_photos === -1 ? __('Unlimited') : number_format($plan->max_photos) }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('Video') }}</span>
                                    <span class="text-xs font-medium text-gray-800 dark:text-gray-100">{{ $plan->max_videos === -1 ? __('Unlimited') : number_format($plan->max_videos) }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('Penyimpanan') }}</span>
                                    <span class="text-xs font-medium text-gray-800 dark:text-gray-100">{{ $plan->getStorageFormatted() }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('Anggota Keluarga') }}</span>
                                    <span class="text-xs font-medium text-gray-800 dark:text-gray-100">{{ $plan->max_family_members === -1 ? __('Unlimited') : $plan->max_family_members }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('Export/Hari') }}</span>
                                    <span class="text-xs font-medium text-gray-800 dark:text-gray-100">{{ $plan->max_export_per_day === -1 ? __('Unlimited') : $plan->max_export_per_day }}</span>
                                </div>
                                @if ($plan->features && count($plan->features) > 0)
                                    <div class="pt-2 border-t border-gray-100 dark:border-gray-700">
                                        @foreach ($plan->features as $feature)
                                            <div class="flex items-center gap-1.5 py-0.5">
                                                <svg class="w-3.5 h-3.5 text-mintGreen-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                                <span class="text-xs text-gray-600 dark:text-gray-400">{{ $feature }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- FAQ Section --}}
        <div class="mt-16 max-w-2xl mx-auto">
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 text-center mb-6">❓ {{ __('Pertanyaan Umum') }}</h2>
            <div class="space-y-4">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-5" x-data="{ open: false }">
                    <button @click="open = !open" class="w-full flex items-center justify-between text-left min-h-[44px]">
                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ __('Bagaimana cara membayar?') }}</span>
                        <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('Setelah memilih paket, Anda akan diarahkan ke halaman pembayaran. Lakukan transfer bank ke rekening yang tercantum, lalu upload bukti transfer. Tim kami akan memverifikasi dalam 1×24 jam.') }}
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-5" x-data="{ open: false }">
                    <button @click="open = !open" class="w-full flex items-center justify-between text-left min-h-[44px]">
                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ __('Bisa ganti paket kapan saja?') }}</span>
                        <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('Ya, Anda dapat upgrade atau downgrade paket kapan saja. Perubahan akan berlaku setelah periode billing saat ini berakhir.') }}
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-5" x-data="{ open: false }">
                    <button @click="open = !open" class="w-full flex items-center justify-between text-left min-h-[44px]">
                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ __('Apakah data saya aman?') }}</span>
                        <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('Ya! ForMysha menggunakan enkripsi data dan backup otomatis harian. Data keluarga Anda adalah prioritas utama kami.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
