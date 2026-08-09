<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <a href="{{ route('timeline.index', $child) }}" class="shrink-0 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight truncate">
                    {{ $timeline->title }}
                </h2>
            </div>
            <a href="{{ route('timeline.edit', [$child, $timeline]) }}" class="shrink-0 inline-flex items-center justify-center gap-1 px-4 py-2.5 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-xl text-sm hover:bg-gray-50 dark:hover:bg-gray-600 transition min-h-[44px]">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                {{ __('Edit') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="p-4 bg-mintGreen-50 dark:bg-mintGreen-950/30 border border-mintGreen-200 dark:border-mintGreen-800 text-mintGreen-700 dark:text-mintGreen-400 rounded-xl" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Timeline Detail Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl">
                <div class="bg-gradient-to-br from-lavender-50 via-cream-50 to-softPink-50 dark:from-lavender-950/30 dark:via-gray-800 dark:to-softPink-950/30 p-6 sm:p-8">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                @if ($timeline->is_featured)
                                    <span class="text-xs px-2 py-0.5 rounded-full bg-warmYellow-100 dark:bg-warmYellow-950/30 text-warmYellow-600 dark:text-warmYellow-400">⭐ Unggulan</span>
                                @endif
                                @if ($timeline->mood)
                                    <span class="text-sm">{{ $timeline->mood_label }}</span>
                                @endif
                            </div>
                            <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $timeline->title }}</h3>
                            <p class="mt-1 text-gray-500 dark:text-gray-400">{{ $timeline->formatted_date }}</p>
                        </div>

                        <!-- Date Badge -->
                        <div class="shrink-0 w-20 h-20 rounded-2xl bg-white/80 dark:bg-gray-700/80 flex flex-col items-center justify-center shadow-soft">
                            <span class="text-sm font-medium text-lavender-500 dark:text-lavender-400">{{ $timeline->event_date->locale('id')->isoFormat('MMM') }}</span>
                            <span class="text-2xl font-bold text-gray-700 dark:text-gray-300">{{ $timeline->event_date->format('d') }}</span>
                            <span class="text-xs text-gray-400 dark:text-gray-500">{{ $timeline->event_date->format('Y') }}</span>
                        </div>
                    </div>
                </div>

                <div class="p-6 sm:p-8 space-y-6">
                    <!-- Description -->
                    @if ($timeline->description)
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">{{ __('Cerita') }}</h4>
                            <p class="text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">{{ $timeline->description }}</p>
                        </div>
                    @endif

                    <!-- Details Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @if ($timeline->event_time)
                            <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-2xl">
                                <span class="text-lg">🕐</span>
                                <div>
                                    <p class="text-xs text-gray-400 dark:text-gray-500">{{ __('Waktu') }}</p>
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $timeline->event_time }}</p>
                                </div>
                            </div>
                        @endif

                        @if ($timeline->location)
                            <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-2xl">
                                <span class="text-lg">📍</span>
                                <div>
                                    <p class="text-xs text-gray-400 dark:text-gray-500">{{ __('Lokasi') }}</p>
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $timeline->location }}</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Tags -->
                    @if ($timeline->tags && count($timeline->tags) > 0)
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">{{ __('Tag') }}</h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($timeline->tags as $tag)
                                    <span class="px-3 py-1 text-sm rounded-full bg-skyBlue-50 dark:bg-skyBlue-950/30 text-skyBlue-500 dark:text-skyBlue-400">#{{ $tag }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Media Section -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">{{ __('Media') }}</h4>

                        {{-- Upload Form --}}
                        <form method="POST" action="{{ route('media.store.timeline', [$child, $timeline]) }}" enctype="multipart/form-data" class="mb-4">
                            @csrf
                            <x-media-upload name="media[]" :multiple="true" />
                            <button type="submit" class="btn-primary text-sm">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ __('Unggah Media') }}
                            </button>
                        </form>

                        @if ($timeline->media->isEmpty())
                            <p class="text-sm text-gray-400 dark:text-gray-500">{{ __('Belum ada media yang dilampirkan.') }}</p>
                        @else
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                @foreach ($timeline->media as $media)
                                    <div class="relative group p-3 bg-gray-50 dark:bg-gray-700/50 rounded-2xl text-center">
                                        <div class="text-2xl mb-1">{{ $media->file_type_label }}</div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $media->file_name }}</p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500">{{ $media->formatted_size }}</p>
                                        <form method="POST" action="{{ route('media.destroy', [$child, $media]) }}" class="absolute top-1 right-1" onsubmit="return confirm('{{ __('Yakin ingin menghapus media ini?') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-8 h-8 rounded-full bg-red-500 text-white flex items-center justify-center hover:bg-red-600 transition text-xs opacity-80 group-hover:opacity-100">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                <a href="{{ route('timeline.index', $child) }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium rounded-xl text-sm hover:bg-gray-200 dark:hover:bg-gray-600 transition min-h-[44px]">
                    ← {{ __('Kembali ke Timeline') }}
                </a>

                <form method="POST" action="{{ route('timeline.destroy', [$child, $timeline]) }}" x-data="{ show: false }" @submit.prevent="if (confirm('Yakin ingin menghapus kenangan ini?')) $el.submit()">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-1 px-4 py-2.5 text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-950/30 font-medium rounded-xl transition text-sm min-h-[44px]">
                        🗑️ {{ __('Hapus Kenangan') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
