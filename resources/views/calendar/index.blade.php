<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <a href="{{ route('children.show', $child) }}" class="text-sm text-skyBlue-600 hover:text-skyBlue-700 dark:text-skyBlue-400 dark:hover:text-skyBlue-300 transition">
                    ← Kembali ke {{ $child->nickname ?? $child->name }}
                </a>
                <h2 class="mt-1 font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                    📅 {{ __('My Calendar') }} — {{ $child->nickname ?? $child->name }}
                </h2>
            </div>
            <a href="{{ route('calendar.create', $child) }}" class="btn-primary min-h-[44px]">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('Tambah Acara') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Calendar Grid View -->
        <div class="mb-6">
            <x-calendar-grid
                :events="$allMonthEvents"
                :current-month="$currentMonth"
                :current-year="$currentYear"
                :child="$child"
            />
        </div>

        <!-- Event List (Paginated) -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl">
            <div class="p-4 sm:p-6 lg:p-8">
                <h3 class="font-semibold text-gray-800 dark:text-gray-100 mb-4">📋 {{ __('Semua Acara') }}</h3>

                @if ($events->isEmpty())
                    <div class="text-center py-12">
                        <div class="text-6xl mb-4">📅</div>
                        <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-2">{{ __('Belum Ada Acara') }}</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-6">{{ __('Jadwalkan acara penting ' . ($child->nickname ?? $child->name) . ' di sini.') }}</p>
                        <a href="{{ route('calendar.create', $child) }}" class="btn-primary min-h-[44px]">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            {{ __('Tambah Acara Pertama') }}
                        </a>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach ($events as $event)
                            <a href="{{ route('calendar.show', [$child, $event]) }}" class="block card-hover p-5 rounded-2xl bg-gradient-to-br from-mintGreen-50/50 to-skyBlue-50/50 dark:from-mintGreen-950/20 dark:to-skyBlue-950/20 border border-mintGreen-100 dark:border-mintGreen-900/30 hover:shadow-medium transition-all duration-200">
                                <div class="flex items-start gap-4">
                                    <div class="flex-shrink-0 w-14 h-14 rounded-xl bg-gradient-to-br from-mintGreen-400 to-skyBlue-400 flex items-center justify-center text-white font-bold text-center leading-tight">
                                        <div>
                                            <div class="text-xs uppercase">{{ \Carbon\Carbon::parse($event->event_date)->locale('id')->isoFormat('MMM') }}</div>
                                            <div class="text-lg">{{ \Carbon\Carbon::parse($event->event_date)->format('d') }}</div>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h4 class="font-semibold text-gray-800 dark:text-gray-100">{{ $event->title }}</h4>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-mintGreen-100 dark:bg-mintGreen-950/30 text-mintGreen-700 dark:text-mintGreen-400">
                                                {{ $event->event_type_label }}
                                            </span>
                                            @if ($event->is_recurring)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-lavender-100 dark:bg-lavender-950/30 text-lavender-700 dark:text-lavender-400">
                                                    🔁 {{ ucfirst($event->recurrence_pattern ?? 'repeat') }}
                                                </span>
                                            @endif
                                        </div>
                                        @if ($event->description)
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 line-clamp-2">{{ Str::limit($event->description, 100) }}</p>
                                        @endif
                                        <div class="mt-2 flex items-center gap-3 text-xs text-gray-400 dark:text-gray-500">
                                            <span>📅 {{ $event->formatted_date }}</span>
                                            @if ($event->event_time)
                                                <span>🕐 {{ $event->formatted_time }}</span>
                                            @endif
                                            @if ($event->reminder_at)
                                                <span>🔔 {{ $event->reminder_at->diffForHumans() }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $events->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
