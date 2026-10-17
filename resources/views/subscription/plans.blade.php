<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            💎 {{ __('subscription.title') }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Write-access notice: shown when redirected here because the subscription is not active --}}
        @if (session('subscription_notice'))
            <div class="mb-6 rounded-xl bg-warmYellow-50 dark:bg-warmYellow-950/30 border border-warmYellow-200 dark:border-warmYellow-900/40 p-4 sm:p-5 flex items-start gap-3" role="alert">
                <span class="text-xl leading-none">💡</span>
                <p class="text-sm font-medium text-warmYellow-700 dark:text-warmYellow-400">
                    {{ session('subscription_notice') }}
                </p>
            </div>
        @endif

        {{-- Header --}}
        <div class="text-center mb-10">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100 mb-2">{{ __('subscription.hero_title') }}</h1>
            <p class="text-gray-500 dark:text-gray-400 max-w-lg mx-auto">{{ __('subscription.hero_description') }}</p>
        </div>

        @php
            $familyPlans = $plans->filter(fn ($plan) => $plan->isForFamilies())->values();
            $facilityPlans = $plans->filter(fn ($plan) => $plan->isForFacilities())->values();
        @endphp

        {{-- Paket untuk Keluarga (B2C) --}}
        @if ($familyPlans->isNotEmpty())
            <section data-audience-section="family" class="mb-12">
                <div class="mb-6">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">👨‍👩‍👧‍👦 {{ __('subscription.section_family_title') }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 max-w-2xl">{{ __('subscription.section_family_description') }}</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($familyPlans as $plan)
                        <x-plan-card :plan="$plan" :index="$loop->index" :is-popular="$plan->isPopular()" />
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Paket untuk Fasilitas Kesehatan (B2B) --}}
        @if ($facilityPlans->isNotEmpty())
            <section data-audience-section="facility" class="mb-12">
                <div class="mb-6">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">🏥 {{ __('subscription.section_facility_title') }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 max-w-2xl">{{ __('subscription.section_facility_description') }}</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($facilityPlans as $plan)
                        <x-plan-card :plan="$plan" :index="$loop->index" :is-popular="$plan->isPopular()" />
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Feature Comparison Table --}}
        <div class="mt-16">
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 text-center mb-6">📊 {{ __('subscription.comparison_title') }}</h2>
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-soft overflow-hidden">
                @php
                    // Fitur bertipe modul (nilai string pada key numerik) — baris centang ✓/✕
                    $moduleFeatureKeys = collect($plans)
                        ->flatMap(fn ($p) => collect($p->features ?? [])->filter(fn ($v, $k) => is_int($k) && is_string($v))->values()->all())
                        ->unique()
                        ->values()
                        ->all();
                    // Fitur bertipe nilai (key => value, mis. max_staff) — baris perbandingan nilai
                    $valueFeatureKeys = collect($plans)
                        ->flatMap(fn ($p) => collect($p->features ?? [])->filter(fn ($v, $k) => ! is_int($k))->keys()->all())
                        ->unique()
                        ->values()
                        ->all();
                    $translator = $plans->first();
                @endphp

                {{-- Desktop Table --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-700">
                                <th class="text-left py-4 px-6 text-sm font-semibold text-gray-500 dark:text-gray-400">{{ __('subscription.feature') }}</th>
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
                                <td class="py-3 px-6 text-sm text-gray-600 dark:text-gray-300">{{ __('subscription.price_per_month') }}</td>
                                @foreach ($plans as $plan)
                                    <td class="py-3 px-4 text-center">
                                        @if ($plan->price_monthly === 0)
                                            <span class="text-sm font-semibold text-mintGreen-600">{{ __('subscription.free') }}</span>
                                        @else
                                            <span class="text-sm font-semibold text-gray-800 dark:text-gray-100">Rp {{ number_format($plan->price_monthly, 0, ',', '.') }}</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                            {{-- Anak --}}
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="py-3 px-6 text-sm text-gray-600 dark:text-gray-300">{{ __('subscription.children') }}</td>
                                @foreach ($plans as $plan)
                                    <td class="py-3 px-4 text-center">
                                        @if ($plan->max_children === -1)
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-mintGreen-100 dark:bg-mintGreen-900/30">
                                                <svg class="w-4 h-4 text-mintGreen-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                            </span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">{{ __('subscription.unlimited') }}</span>
                                        @else
                                            <span class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $plan->max_children }}</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                            {{-- Foto --}}
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="py-3 px-6 text-sm text-gray-600 dark:text-gray-300">{{ __('subscription.photos') }}</td>
                                @foreach ($plans as $plan)
                                    <td class="py-3 px-4 text-center">
                                        @if ($plan->max_photos === -1)
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-mintGreen-100 dark:bg-mintGreen-900/30">
                                                <svg class="w-4 h-4 text-mintGreen-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                            </span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">{{ __('subscription.unlimited') }}</span>
                                        @else
                                            <span class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ number_format($plan->max_photos) }}</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                            {{-- Video --}}
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="py-3 px-6 text-sm text-gray-600 dark:text-gray-300">{{ __('subscription.videos') }}</td>
                                @foreach ($plans as $plan)
                                    <td class="py-3 px-4 text-center">
                                        @if ($plan->max_videos === -1)
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-mintGreen-100 dark:bg-mintGreen-900/30">
                                                <svg class="w-4 h-4 text-mintGreen-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                            </span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">{{ __('subscription.unlimited') }}</span>
                                        @else
                                            <span class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ number_format($plan->max_videos) }}</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                            {{-- Storage --}}
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="py-3 px-6 text-sm text-gray-600 dark:text-gray-300">{{ __('subscription.storage') }}</td>
                                @foreach ($plans as $plan)
                                    <td class="py-3 px-4 text-center">
                                        <span class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $plan->getStorageFormatted() }}</span>
                                    </td>
                                @endforeach
                            </tr>
                            {{-- Anggota Keluarga --}}
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="py-3 px-6 text-sm text-gray-600 dark:text-gray-300">{{ __('subscription.family_members') }}</td>
                                @foreach ($plans as $plan)
                                    <td class="py-3 px-4 text-center">
                                        @if ($plan->max_family_members === -1)
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-mintGreen-100 dark:bg-mintGreen-900/30">
                                                <svg class="w-4 h-4 text-mintGreen-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                            </span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">{{ __('subscription.unlimited') }}</span>
                                        @else
                                            <span class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $plan->max_family_members }}</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                            {{-- Export per Hari --}}
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="py-3 px-6 text-sm text-gray-600 dark:text-gray-300">{{ __('subscription.export_per_day') }}</td>
                                @foreach ($plans as $plan)
                                    <td class="py-3 px-4 text-center">
                                        @if ($plan->max_export_per_day === -1)
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-mintGreen-100 dark:bg-mintGreen-900/30">
                                                <svg class="w-4 h-4 text-mintGreen-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                            </span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">{{ __('subscription.unlimited') }}</span>
                                        @else
                                            <span class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $plan->max_export_per_day }}</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                            {{-- Fitur modul (✓/✕) --}}
                            @foreach ($moduleFeatureKeys as $featureKey)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                                    <td class="py-3 px-6 text-sm text-gray-600 dark:text-gray-300">{{ $translator->featureLabel($featureKey) }}</td>
                                    @foreach ($plans as $plan)
                                        <td class="py-3 px-4 text-center">
                                            @if (collect($plan->features ?? [])->values()->contains($featureKey))
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
                            {{-- Fitur bernilai (mis. max_staff, referrals_enabled) --}}
                            @foreach ($valueFeatureKeys as $featureKey)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                                    <td class="py-3 px-6 text-sm text-gray-600 dark:text-gray-300">{{ $translator->featureLabel($featureKey) }}</td>
                                    @foreach ($plans as $plan)
                                        @php
                                            $hasFeature = array_key_exists($featureKey, $plan->features ?? []);
                                            $featureValue = $plan->features[$featureKey] ?? null;
                                        @endphp
                                        <td class="py-3 px-4 text-center">
                                            @if (! $hasFeature)
                                                <span class="text-xs text-gray-400 dark:text-gray-500">—</span>
                                            @elseif (is_bool($featureValue))
                                                @if ($featureValue)
                                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-mintGreen-100 dark:bg-mintGreen-900/30">
                                                        <svg class="w-4 h-4 text-mintGreen-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gray-100 dark:bg-gray-700">
                                                        <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                                    </span>
                                                @endif
                                            @elseif ($featureValue === null || $featureValue === -1)
                                                <span class="text-xs text-mintGreen-600 font-medium">{{ __('subscription.unlimited') }}</span>
                                            @elseif (is_numeric($featureValue))
                                                <span class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ number_format((float) $featureValue) }}</span>
                                            @else
                                                <span class="text-xs text-gray-600 dark:text-gray-300">{{ $featureValue }}</span>
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
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('subscription.price_per_month') }}</span>
                                    @if ($plan->price_monthly === 0)
                                        <span class="text-xs font-semibold text-mintGreen-600">{{ __('subscription.free') }}</span>
                                    @else
                                        <span class="text-xs font-semibold text-gray-800 dark:text-gray-100">Rp {{ number_format($plan->price_monthly, 0, ',', '.') }}</span>
                                    @endif
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('subscription.children') }}</span>
                                    <span class="text-xs font-medium text-gray-800 dark:text-gray-100">{{ $plan->max_children === -1 ? __('subscription.unlimited') : $plan->max_children }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('subscription.photos') }}</span>
                                    <span class="text-xs font-medium text-gray-800 dark:text-gray-100">{{ $plan->max_photos === -1 ? __('subscription.unlimited') : number_format($plan->max_photos) }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('subscription.videos') }}</span>
                                    <span class="text-xs font-medium text-gray-800 dark:text-gray-100">{{ $plan->max_videos === -1 ? __('subscription.unlimited') : number_format($plan->max_videos) }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('subscription.storage') }}</span>
                                    <span class="text-xs font-medium text-gray-800 dark:text-gray-100">{{ $plan->getStorageFormatted() }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('subscription.family_members') }}</span>
                                    <span class="text-xs font-medium text-gray-800 dark:text-gray-100">{{ $plan->max_family_members === -1 ? __('subscription.unlimited') : $plan->max_family_members }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('subscription.export_per_day') }}</span>
                                    <span class="text-xs font-medium text-gray-800 dark:text-gray-100">{{ $plan->max_export_per_day === -1 ? __('subscription.unlimited') : $plan->max_export_per_day }}</span>
                                </div>
                                @if ($plan->featureLabels() !== [])
                                    <div class="pt-2 border-t border-gray-100 dark:border-gray-700">
                                        @foreach ($plan->featureLabels() as $featureLabel)
                                            <div class="flex items-center gap-1.5 py-0.5">
                                                <svg class="w-3.5 h-3.5 text-mintGreen-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                                <span class="text-xs text-gray-600 dark:text-gray-400">{{ $featureLabel }}</span>
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

        {{-- FAQ Section — disesuaikan dengan perilaku aktual aplikasi --}}
        <div class="mt-16 max-w-2xl mx-auto">
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 text-center mb-6">❓ {{ __('subscription.faq_title') }}</h2>
            <div class="space-y-4">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-5" x-data="{ open: false }">
                    <button @click="open = !open" class="w-full flex items-center justify-between text-left min-h-[44px]">
                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ __('subscription.faq_payment_q') }}</span>
                        <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('subscription.faq_payment_a') }}
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-5" x-data="{ open: false }">
                    <button @click="open = !open" class="w-full flex items-center justify-between text-left min-h-[44px]">
                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ __('subscription.faq_switch_q') }}</span>
                        <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('subscription.faq_switch_a') }}
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-5" x-data="{ open: false }">
                    <button @click="open = !open" class="w-full flex items-center justify-between text-left min-h-[44px]">
                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ __('subscription.faq_inactive_q') }}</span>
                        <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('subscription.faq_inactive_a') }}
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-5" x-data="{ open: false }">
                    <button @click="open = !open" class="w-full flex items-center justify-between text-left min-h-[44px]">
                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ __('subscription.faq_security_q') }}</span>
                        <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('subscription.faq_security_a') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
