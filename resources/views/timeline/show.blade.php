<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('timeline.index', $child) }}" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $timeline->title }}
                </h2>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('timeline.edit', [$child, $timeline]) }}" class="btn-secondary text-sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    {{ __('Edit') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="p-4 bg-mintGreen-50 border border-mintGreen-200 text-mintGreen-700 rounded-xl" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Timeline Detail Card -->
            <div class="bg-white overflow-hidden shadow-soft sm:rounded-3xl">
                <div class="bg-gradient-to-br from-lavender-50 via-cream-50 to-softPink-50 p-6 sm:p-8">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                @if ($timeline->is_featured)
                                    <span class="text-xs px-2 py-0.5 rounded-full bg-warmYellow-100 text-warmYellow-600">⭐ Unggulan</span>
                                @endif
                                @if ($timeline->mood)
                                    <span class="text-sm">{{ $timeline->mood_label }}</span>
                                @endif
                            </div>
                            <h3 class="text-2xl font-bold text-gray-800">{{ $timeline->title }}</h3>
                            <p class="mt-1 text-gray-500">{{ $timeline->formatted_date }}</p>
                        </div>

                        <!-- Date Badge -->
                        <div class="shrink-0 w-20 h-20 rounded-2xl bg-white/80 flex flex-col items-center justify-center shadow-soft">
                            <span class="text-sm font-medium text-lavender-500">{{ $timeline->event_date->locale('id')->isoFormat('MMM') }}</span>
                            <span class="text-2xl font-bold text-gray-700">{{ $timeline->event_date->format('d') }}</span>
                            <span class="text-xs text-gray-400">{{ $timeline->event_date->format('Y') }}</span>
                        </div>
                    </div>
                </div>

                <div class="p-6 sm:p-8 space-y-6">
                    <!-- Description -->
                    @if ($timeline->description)
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-2">{{ __('Cerita') }}</h4>
                            <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $timeline->description }}</p>
                        </div>
                    @endif

                    <!-- Details Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @if ($timeline->event_time)
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-2xl">
                                <span class="text-lg">🕐</span>
                                <div>
                                    <p class="text-xs text-gray-400">{{ __('Waktu') }}</p>
                                    <p class="text-sm font-medium text-gray-700">{{ $timeline->event_time }}</p>
                                </div>
                            </div>
                        @endif

                        @if ($timeline->location)
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-2xl">
                                <span class="text-lg">📍</span>
                                <div>
                                    <p class="text-xs text-gray-400">{{ __('Lokasi') }}</p>
                                    <p class="text-sm font-medium text-gray-700">{{ $timeline->location }}</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Tags -->
                    @if ($timeline->tags && count($timeline->tags) > 0)
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-2">{{ __('Tag') }}</h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($timeline->tags as $tag)
                                    <span class="px-3 py-1 text-sm rounded-full bg-skyBlue-50 text-skyBlue-500">#{{ $tag }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Media Section -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-3">{{ __('Media') }}</h4>
                        @if ($timeline->media->isEmpty())
                            <p class="text-sm text-gray-400">{{ __('Belum ada media yang dilampirkan.') }}</p>
                        @else
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                @foreach ($timeline->media as $media)
                                    <div class="p-3 bg-gray-50 rounded-2xl text-center">
                                        <div class="text-2xl mb-1">{{ $media->file_type_label }}</div>
                                        <p class="text-xs text-gray-500 truncate">{{ $media->file_name }}</p>
                                        <p class="text-xs text-gray-400">{{ $media->formatted_size }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between">
                <a href="{{ route('timeline.index', $child) }}" class="btn-secondary">
                    ← {{ __('Kembali ke Timeline') }}
                </a>

                <form method="POST" action="{{ route('timeline.destroy', [$child, $timeline]) }}" x-data="{ show: false }" @submit.prevent="if (confirm('Yakin ingin menghapus kenangan ini?')) $el.submit()">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-sm text-red-500 hover:text-red-700 transition">
                        🗑️ {{ __('Hapus Kenangan') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
