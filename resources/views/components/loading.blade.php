@props(['size' => 'md', 'text' => null])

@php
    $sizeClasses = match($size) {
        'sm' => 'w-5 h-5',
        'md' => 'w-8 h-8',
        'lg' => 'w-12 h-12',
        default => 'w-8 h-8',
    };
@endphp

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center gap-3 py-8']) }}>
    <div class="{{ $sizeClasses }} border-4 border-softPink-200 dark:border-softPink-900/30 border-t-softPink-400 dark:border-t-softPink-400 rounded-full animate-spin"></div>
    @if ($text)
        <p class="text-sm text-gray-400 dark:text-gray-500">{{ $text }}</p>
    @endif
</div>
