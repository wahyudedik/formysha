@props([
    'events' => collect(),
    'currentMonth' => null,
    'currentYear' => null,
    'child' => null,
])

@php
    $month = $currentMonth ?? (int) now()->format('m');
    $year = $currentYear ?? (int) now()->format('Y');
    $firstDay = \Carbon\Carbon::create($year, $month, 1);
    $daysInMonth = $firstDay->daysInMonth;
    $startDayOfWeek = $firstDay->dayOfWeek; // 0=Sun
    $monthName = $firstDay->locale('id')->isoFormat('MMMM YYYY');

    // Group events by day
    $eventsByDay = $events->groupBy(fn ($event) => \Carbon\Carbon::parse($event->event_date)->format('d'));

    // Previous/Next month navigation
    $prevMonth = $month - 1;
    $prevYear = $year;
    if ($prevMonth < 1) {
        $prevMonth = 12;
        $prevYear--;
    }
    $nextMonth = $month + 1;
    $nextYear = $year;
    if ($nextMonth > 12) {
        $nextMonth = 1;
        $nextYear++;
    }

    $dayLabels = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
@endphp

<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl" x-data="{ view: 'grid' }">
    <div class="p-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
            <div class="flex items-center gap-3">
                <button @click="view = view === 'grid' ? 'list' : 'grid'" class="px-3 py-1.5 rounded-xl text-sm font-medium transition {{ 'bg-skyBlue-100 text-skyBlue-700 dark:bg-skyBlue-950/30 dark:text-skyBlue-300' }}">
                    <span x-show="view === 'grid'">📋 Daftar</span>
                    <span x-show="view === 'list'">📅 Grid</span>
                </button>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('calendar.index', ['child' => $child, 'month' => $prevMonth, 'year' => $prevYear]) }}"
                   class="p-2 min-h-[44px] min-w-[44px] inline-flex items-center justify-center rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                     <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                     </svg>
                 </a>
                 <span class="text-sm font-semibold text-gray-800 dark:text-gray-100 min-w-[160px] text-center">{{ $monthName }}</span>
                 <a href="{{ route('calendar.index', ['child' => $child, 'month' => $nextMonth, 'year' => $nextYear]) }}"
                    class="p-2 min-h-[44px] min-w-[44px] inline-flex items-center justify-center rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                     <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>

        <!-- Grid View -->
        <div x-show="view === 'grid'" x-transition>
            <div class="grid grid-cols-7 gap-px bg-gray-200 dark:bg-gray-700 rounded-xl overflow-hidden">
                @foreach ($dayLabels as $label)
                    <div class="bg-gray-50 dark:bg-gray-700 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-400">
                        {{ $label }}
                    </div>
                @endforeach

                @php
                    // Fill empty cells before first day
                    for ($i = 0; $i < $startDayOfWeek; $i++) {
                        echo '<div class="bg-white dark:bg-gray-800 p-2 min-h-[80px] opacity-30"></div>';
                    }
                @endphp

                @for ($day = 1; $day <= $daysInMonth; $day++)
                    @php
                        $isToday = now()->format('Y-m-d') === $year.'-'.str_pad($month, 2, '0', STR_PAD_LEFT).'-'.str_pad($day, 2, '0', STR_PAD_LEFT);
                        $dayEvents = $eventsByDay->get(str_pad($day, 2, '0', STR_PAD_LEFT), collect());
                    @endphp
                    <div class="bg-white dark:bg-gray-800 p-2 min-h-[80px] {{ $isToday ? 'bg-softPink-50 dark:bg-softPink-950/30 ring-2 ring-softPink-300 dark:ring-softPink-700' : 'hover:bg-gray-50 dark:hover:bg-gray-750' }} transition cursor-default">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-medium {{ $isToday ? 'text-softPink-600 dark:text-softPink-400' : 'text-gray-700 dark:text-gray-300' }}">{{ $day }}</span>
                            @if ($dayEvents->isNotEmpty())
                                <span class="w-1.5 h-1.5 rounded-full bg-mintGreen-400"></span>
                            @endif
                        </div>
                        <div class="space-y-0.5">
                            @foreach ($dayEvents->take(2) as $event)
                                <a href="{{ route('calendar.show', [$child, $event]) }}" class="block px-1 py-0.5 text-[10px] rounded bg-mintGreen-100 text-mintGreen-700 dark:bg-mintGreen-950/30 dark:text-mintGreen-400 truncate hover:bg-mintGreen-200 dark:hover:bg-mintGreen-900/40 transition">
                                    {{ $event->title }}
                                </a>
                            @endforeach
                            @if ($dayEvents->count() > 2)
                                <span class="block px-1 text-[9px] text-gray-400 dark:text-gray-500">+{{ $dayEvents->count() - 2 }} lagi</span>
                            @endif
                        </div>
                    </div>
                @endfor

                @php
                    // Fill remaining cells
                    $totalCells = $startDayOfWeek + $daysInMonth;
                    $remaining = (7 - ($totalCells % 7)) % 7;
                    for ($i = 0; $i < $remaining; $i++) {
                        echo '<div class="bg-white dark:bg-gray-800 p-2 min-h-[80px] opacity-30"></div>';
                    }
                @endphp
            </div>
        </div>

        <!-- List View -->
        <div x-show="view === 'list'" x-transition>
            @if ($events->isEmpty())
                <div class="text-center py-8">
                    <div class="text-3xl mb-2">📅</div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada acara di bulan ini.</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach ($events as $event)
                        <a href="{{ route('calendar.show', [$child, $event]) }}" class="block p-4 rounded-xl border border-gray-100 dark:border-gray-700 hover:bg-mintGreen-50 dark:hover:bg-mintGreen-950/20 hover:border-mintGreen-200 dark:hover:border-mintGreen-800 transition">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-mintGreen-400 to-skyBlue-400 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                    {{ \Carbon\Carbon::parse($event->event_date)->format('d') }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-medium text-gray-800 dark:text-gray-200 text-sm">{{ $event->title }}</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $event->event_type_label }} · {{ $event->formatted_date }}</p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
