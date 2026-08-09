@props([
    'type' => 'card',
    'count' => 3,
    'lines' => 3,
])

@php
    $typeClasses = match($type) {
        'card' => 'h-24 rounded-2xl',
        'list-item' => 'h-16 rounded-xl',
        'avatar' => 'w-12 h-12 rounded-full',
        'title' => 'h-6 w-1/3 rounded-lg',
        'text' => 'h-4 rounded-lg',
        'table-row' => 'h-12 rounded-lg',
        default => 'h-24 rounded-2xl',
    };
@endphp

<div class="animate-pulse space-y-4" {{ $attributes }}>
    @for ($i = 0; $i < $count; $i++)
        @if ($type === 'card')
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-soft border border-gray-50 dark:border-gray-700">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-softPink-100 dark:bg-softPink-900/30 rounded-full"></div>
                    <div class="flex-1 space-y-2">
                        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded-lg w-1/3"></div>
                        <div class="h-3 bg-gray-100 dark:bg-gray-700/50 rounded-lg w-1/2"></div>
                    </div>
                </div>
                <div class="mt-4 flex gap-2">
                    <div class="h-6 bg-lavender-100 dark:bg-lavender-900/30 rounded-lg w-16"></div>
                    <div class="h-6 bg-peach-100 dark:bg-peach-900/30 rounded-lg w-16"></div>
                    <div class="h-6 bg-skyBlue-100 dark:bg-skyBlue-900/30 rounded-lg w-16"></div>
                </div>
            </div>
        @elseif ($type === 'list-item')
            <div class="flex items-center gap-4 p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-50 dark:border-gray-700">
                <div class="w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded-xl flex-shrink-0"></div>
                <div class="flex-1 space-y-2">
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded-lg w-2/3"></div>
                    <div class="h-3 bg-gray-100 dark:bg-gray-700/50 rounded-lg w-1/3"></div>
                </div>
            </div>
        @elseif ($type === 'table-row')
            <div class="flex items-center gap-4 p-3 bg-white dark:bg-gray-800 rounded-lg">
                <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded-lg w-1/6"></div>
                <div class="h-4 bg-gray-100 dark:bg-gray-700/50 rounded-lg w-1/6"></div>
                <div class="h-4 bg-gray-100 dark:bg-gray-700/50 rounded-lg w-1/6"></div>
                <div class="h-4 bg-gray-100 dark:bg-gray-700/50 rounded-lg w-1/6"></div>
                <div class="h-4 bg-gray-100 dark:bg-gray-700/50 rounded-lg w-1/6"></div>
            </div>
        @else
            <div class="space-y-2">
                @for ($j = 0; $j < $lines; $j++)
                    <div class="{{ $typeClasses }} bg-gray-200 dark:bg-gray-700" style="width: {{ rand(50, 100) }}%"></div>
                @endfor
            </div>
        @endif
    @endfor
</div>
