@props(['items' => []])

@php
    $breadcrumbItems = is_array($items) ? collect($items) : $items;
@endphp

@if ($breadcrumbItems->isNotEmpty())
    <nav class="flex items-center text-sm text-gray-400 dark:text-gray-500 mb-4 overflow-x-auto" aria-label="Breadcrumb">
        <ol class="flex items-center gap-1.5 whitespace-nowrap">
            @foreach ($breadcrumbItems as $index => $item)
                @if ($index > 0)
                    <svg class="w-3.5 h-3.5 text-gray-300 dark:text-gray-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                @endif
                @if (isset($item['url']) && $index !== $breadcrumbItems->count() - 1)
                    <a href="{{ $item['url'] }}" class="hover:text-gray-600 dark:hover:text-gray-300 transition font-medium">
                        {{ $item['label'] }}
                    </a>
                @else
                    <span class="text-gray-600 dark:text-gray-300 font-medium">{{ $item['label'] }}</span>
                @endif
            @endforeach
        </ol>
    </nav>
@endif
