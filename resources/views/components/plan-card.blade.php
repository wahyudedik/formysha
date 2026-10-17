@props(['plan', 'index', 'isPopular' => false])

@php
    $isFree = $plan->price_monthly === 0;
    $isCustomPricing = $plan->isCustomPricing();
    $gradients = [
        'from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-750',
        'from-skyBlue-50 to-skyBlue-100 dark:from-skyBlue-950/30 dark:to-skyBlue-900/30',
        'from-softPink-50 to-lavender-100 dark:from-softPink-950/30 dark:to-lavender-950/30',
        'from-lavender-50 to-softPink-100 dark:from-lavender-950/30 dark:to-softPink-950/30',
    ];
    $gradient = $gradients[$index % count($gradients)];
    $borderColors = [
        'border-gray-200',
        'border-skyBlue-200',
        'border-softPink-200',
        'border-lavender-200',
    ];
    $borderColor = $borderColors[$index % count($borderColors)];
    $buttonColors = [
        'bg-gray-600 hover:bg-gray-700',
        'bg-skyBlue-400 hover:bg-skyBlue-500',
        'bg-softPink-400 hover:bg-softPink-500',
        'bg-lavender-400 hover:bg-lavender-500',
    ];
    $buttonColor = $buttonColors[$index % count($buttonColors)];
@endphp

<div {{ $attributes->merge(['class' => 'relative bg-gradient-to-br '.$gradient.' rounded-3xl border '.$borderColor.' dark:border-gray-600 overflow-hidden transition-all duration-300 hover:shadow-soft-lg hover:-translate-y-1 dark:bg-gray-800']) }}>
    {{-- Popular Badge (rekomendasi utama) --}}
    @if ($isPopular)
        <div class="absolute top-0 right-0" data-plan-popular="{{ $plan->slug }}">
            <div class="bg-softPink-400 text-white text-[10px] font-bold px-3 py-1 rounded-bl-xl">
                ⭐ {{ __('subscription.popular_badge') }}
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
                    <span class="text-3xl font-bold text-gray-800 dark:text-gray-100">{{ __('subscription.free') }}</span>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('subscription.free_forever') }}</p>
            @else
                <div class="flex items-baseline gap-1">
                    <span class="text-xs text-gray-500 dark:text-gray-400">Rp</span>
                    <span class="text-3xl font-bold text-gray-800 dark:text-gray-100">{{ number_format($plan->price_monthly, 0, ',', '.') }}</span>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('subscription.per_month') }}</p>
                @if ($plan->price_yearly)
                    <p class="text-xs text-mintGreen-600 font-medium mt-1">
                        💡 {{ __('subscription.yearly_saving', [
                            'amount' => number_format($plan->price_yearly, 0, ',', '.'),
                            'percent' => round((1 - $plan->price_yearly / ($plan->price_monthly * 12)) * 100),
                        ]) }}
                    </p>
                @endif
            @endif
        </div>

        {{-- Limits --}}
        <div class="space-y-2 mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                <span class="text-mintGreen-500">✓</span>
                {{ $plan->max_children === -1 ? __('subscription.unlimited_children') : $plan->max_children . ' ' . __('subscription.children') }}
            </div>
            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                <span class="text-mintGreen-500">✓</span>
                {{ $plan->max_photos === -1 ? __('subscription.unlimited_photos') : $plan->max_photos . ' ' . __('subscription.photos') }}
            </div>
            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                <span class="text-mintGreen-500">✓</span>
                {{ $plan->max_videos === -1 ? __('subscription.unlimited_videos') : $plan->max_videos . ' ' . __('subscription.videos') }}
            </div>
            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                <span class="text-mintGreen-500">✓</span>
                {{ $plan->getStorageFormatted() }} {{ __('subscription.storage_label') }}
            </div>
        </div>

        {{-- Features (label manusiawi dari kolom features JSON) --}}
        @if ($plan->featureLabels() !== [])
            <div class="space-y-2 mb-6 pt-4 border-t border-gray-200/50 dark:border-gray-600/50">
                @foreach ($plan->featureLabels() as $featureLabel)
                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                        <span class="text-lavender-400">✨</span>
                        {{ $featureLabel }}
                    </div>
                @endforeach
            </div>
        @endif

        {{-- CTA Button --}}
        @if ($isCustomPricing)
            {{-- Plan custom pricing (harga Rp0 tetapi bukan plan gratis) — tidak bisa diaktifkan otomatis --}}
            <button type="button" disabled class="w-full py-3 min-h-[44px] rounded-xl text-white font-semibold text-sm bg-gray-400 dark:bg-gray-500 cursor-not-allowed inline-flex items-center justify-center gap-2">
                📩 {{ __('subscription.contact_us') }}
            </button>
        @else
            <form method="POST" action="{{ route('subscription.subscribe', $plan) }}" x-data="{ loading: false }" @submit="loading = true">
                @csrf
                <button type="submit" x-bind:disabled="loading" class="w-full py-3 min-h-[44px] rounded-xl text-white font-semibold text-sm {{ $buttonColor }} transition-all duration-200 shadow-soft hover:shadow-soft-md disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center justify-center gap-2">
                    <svg x-show="loading" class="w-4 h-4 shrink-0 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    @if ($isFree)
                        <span x-text="loading ? '{{ __('subscription.activating') }}' : '{{ __('subscription.starting_free') }}'"></span>
                    @else
                        <span x-text="loading ? '{{ __('subscription.processing') }}' : '{{ __('subscription.choose_this_plan') }}'"></span>
                    @endif
                </button>
            </form>
        @endif
    </div>
</div>
