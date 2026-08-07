@props(['title', 'subtitle' => null, 'backUrl' => null])

<div class="mb-6">
    @if ($backUrl)
        <a href="{{ $backUrl }}" class="inline-flex items-center text-sm text-gray-400 hover:text-gray-600 transition mb-3 group">
            <svg class="w-4 h-4 mr-1 transform group-hover:-translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            {{ __('Kembali') }}
        </a>
    @endif
    <h1 class="text-2xl font-bold text-gray-800">{{ $title }}</h1>
    @if ($subtitle)
        <p class="text-gray-500 mt-1">{{ $subtitle }}</p>
    @endif
</div>
