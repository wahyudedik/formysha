<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            💎 {{ __('subscription.current_title') }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-breadcrumb :items="[
            ['label' => __('Dashboard'), 'url' => route('dashboard')],
            ['label' => __('subscription.subscription_word')],
        ]" />

        @if ($subscription)
            {{-- Current Plan Card --}}
            <div class="bg-gradient-to-br from-softPink-50 via-white to-lavender-50 dark:from-softPink-950/30 dark:via-gray-800 dark:to-lavender-950/30 rounded-3xl border border-softPink-100 dark:border-softPink-900/50 overflow-hidden mb-6">
                <div class="p-6 sm:p-8">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $subscription->plan->name ?? '-' }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('subscription.active_plan_description') }}</p>
                        </div>
                        <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-sm font-medium self-start
                            {{ match($subscription->status) {
                                'active' => 'bg-mintGreen-100 text-mintGreen-600 dark:bg-mintGreen-950/30 dark:text-mintGreen-400',
                                'pending' => 'bg-warmYellow-100 text-warmYellow-600 dark:bg-warmYellow-950/30 dark:text-warmYellow-400',
                                'inactive' => 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400',
                                'cancelled' => 'bg-red-100 text-red-600 dark:bg-red-950/30 dark:text-red-400',
                                'past_due' => 'bg-orange-100 text-orange-600 dark:bg-orange-950/30 dark:text-orange-400',
                                default => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
                            } }}">
                            {{ match($subscription->status) {
                                'active' => '✅ ' . __('subscription.status_active'),
                                'pending' => '⏳ ' . __('subscription.status_pending'),
                                'inactive' => '⏸️ ' . __('subscription.status_inactive'),
                                'cancelled' => '❌ ' . __('subscription.status_cancelled'),
                                'past_due' => '⚠️ ' . __('subscription.status_past_due'),
                                default => ucfirst($subscription->status),
                            } }}
                        </span>
                    </div>

                    {{-- Plan Details --}}
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div class="bg-white/80 dark:bg-gray-700/50 rounded-xl p-4">
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('subscription.price') }}</p>
                            <p class="text-lg font-bold text-gray-800 dark:text-gray-100">{{ $subscription->plan->getPriceMonthlyFormatted() }}</p>
                            <p class="text-[10px] text-gray-400 dark:text-gray-500">{{ __('subscription.per_month') }}</p>
                        </div>
                        <div class="bg-white/80 dark:bg-gray-700/50 rounded-xl p-4">
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('subscription.start_date') }}</p>
                            <p class="text-sm font-bold text-gray-800 dark:text-gray-100">{{ $subscription->starts_at?->locale('id')->isoFormat('D MMM YYYY') ?? '-' }}</p>
                        </div>
                        <div class="bg-white/80 dark:bg-gray-700/50 rounded-xl p-4">
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('subscription.end_date') }}</p>
                            <p class="text-sm font-bold text-gray-800 dark:text-gray-100">{{ $subscription->ends_at?->locale('id')->isoFormat('D MMM YYYY') ?? '-' }}</p>
                        </div>
                        <div class="bg-white/80 dark:bg-gray-700/50 rounded-xl p-4">
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('subscription.days_remaining') }}</p>
                            <p class="text-lg font-bold text-gray-800 dark:text-gray-100">{{ $subscription->daysRemaining() }}</p>
                        </div>
                    </div>

                    {{-- Progress Bar --}}
                    @if ($subscription->starts_at && $subscription->ends_at)
                        @php
                            $totalDays = $subscription->starts_at->diffInDays($subscription->ends_at);
                            $elapsed = $subscription->starts_at->diffInDays(now());
                            $percent = $totalDays > 0 ? min(100, round(($elapsed / $totalDays) * 100)) : 0;
                        @endphp
                        <div class="mt-6">
                            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mb-1">
                                <span>{{ __('subscription.usage') }}</span>
                                <span>{{ $percent }}%</span>
                            </div>
                            <div class="w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-softPink-400 to-lavender-400 rounded-full transition-all" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Pending Payment Banner --}}
            @if ($subscription->status === 'pending')
                <div class="bg-gradient-to-r from-warmYellow-50 to-peach-50 dark:from-warmYellow-950/30 dark:to-peach-950/30 border border-warmYellow-200 dark:border-warmYellow-800 rounded-2xl p-4 sm:p-6 mb-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="text-3xl">⏳</span>
                            <div>
                                <h4 class="font-semibold text-gray-800 dark:text-gray-100">{{ __('subscription.status_pending') }}</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('subscription.pending_payment_hint') }}</p>
                            </div>
                        </div>
                        <a href="{{ route('subscription.payment.upload', $subscription) }}" class="btn-primary text-sm min-h-[44px] inline-flex items-center whitespace-nowrap">
                            💳 {{ __('subscription.upload_proof_short') }}
                        </a>
                    </div>
                </div>
            @endif

            {{-- Usage Summary --}}
            @if ($subscription->plan && $subscription->status === 'active')
                @php
                    $childrenCount = $tenant ? $tenant->children()->count() : 0;
                @endphp
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft overflow-hidden mb-6">
                    <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-100">📊 {{ __('subscription.usage_summary') }}</h3>
                    </div>
                    <div class="p-4 sm:p-6 grid grid-cols-2 sm:grid-cols-3 gap-4">
                        <div class="text-center p-3 rounded-xl bg-skyBlue-50 dark:bg-skyBlue-950/30">
                            <p class="text-xl font-bold text-skyBlue-600 dark:text-skyBlue-400">{{ $childrenCount }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('subscription.children') }}</p>
                            <p class="text-[10px] text-gray-400 dark:text-gray-500">/ {{ $subscription->plan->max_children === -1 ? '∞' : $subscription->plan->max_children }}</p>
                        </div>
                        <div class="text-center p-3 rounded-xl bg-softPink-50 dark:bg-softPink-950/30">
                            <p class="text-xl font-bold text-softPink-600 dark:text-softPink-400">{{ $tenant?->payments()->where('status', 'approved')->count() ?? 0 }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('subscription.payments') }}</p>
                        </div>
                        <div class="text-center p-3 rounded-xl bg-lavender-50 dark:bg-lavender-950/30">
                            <p class="text-xl font-bold text-lavender-600 dark:text-lavender-400">{{ $subscription->plan->getStorageFormatted() }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('subscription.storage_label') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Actions --}}
            <div class="flex flex-col sm:flex-row flex-wrap gap-3">
                @if ($subscription->status === 'pending')
                    <a href="{{ route('subscription.payment.upload', $subscription) }}" class="btn-primary text-sm min-h-[44px] inline-flex items-center">
                        💳 {{ __('subscription.upload_proof') }}
                    </a>
                @endif
                <a href="{{ route('subscription.plans') }}" class="btn-accent text-sm min-h-[44px] inline-flex items-center">
                    🔄 {{ __('subscription.upgrade_change_plan') }}
                </a>
                <a href="{{ route('subscription.history') }}" class="btn-secondary text-sm min-h-[44px] inline-flex items-center">
                    📜 {{ __('subscription.history') }}
                </a>
            </div>
        @else
            {{-- No Active Subscription --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft overflow-hidden">
                <div class="p-6 sm:p-12 text-center">
                    <div class="text-6xl mb-4">💎</div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-2">{{ __('subscription.no_subscription_title') }}</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-6 max-w-md mx-auto">{{ __('subscription.no_subscription_desc') }}</p>
                    <a href="{{ route('subscription.plans') }}" class="btn-primary text-sm min-h-[44px] inline-flex items-center">
                        💎 {{ __('subscription.view_plans') }}
                    </a>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
