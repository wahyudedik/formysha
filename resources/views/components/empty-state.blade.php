@props(['icon' => '📭', 'title' => 'Belum Ada Data', 'description' => null, 'actionUrl' => null, 'actionText' => null])

<div class="text-center py-12 px-6">
    <div class="text-5xl mb-4">{{ $icon }}</div>
    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-2">{{ $title }}</h3>
    @if ($description)
        <p class="text-gray-400 dark:text-gray-500 text-sm max-w-md mx-auto mb-4">{{ $description }}</p>
    @endif
    @if ($actionUrl && $actionText)
        <a href="{{ $actionUrl }}" class="btn-primary inline-flex items-center text-sm">
            {{ $actionText }}
        </a>
    @endif
</div>
