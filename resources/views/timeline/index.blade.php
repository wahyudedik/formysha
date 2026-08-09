<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <a href="{{ route('children.show', $child) }}" class="shrink-0 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight truncate">
                    📅 {{ __('Timeline') }} — {{ $child->nickname ?? $child->name }}
                </h2>
            </div>
            <a href="{{ route('timeline.create', $child) }}" class="shrink-0 inline-flex items-center justify-center gap-1 px-4 py-2.5 bg-softPink-500 hover:bg-softPink-600 text-white font-medium rounded-xl text-sm shadow-soft transition min-h-[44px]">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('Tambah Kenangan') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 p-4 bg-mintGreen-50 dark:bg-mintGreen-950/30 border border-mintGreen-200 dark:border-mintGreen-800 text-mintGreen-700 dark:text-mintGreen-400 rounded-xl" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)">
                    {{ session('status') }}
                </div>
            @endif

            @if ($timelines->isEmpty())
                <!-- Empty State -->
                <div class="text-center py-16">
                    <div class="text-6xl mb-4">📸</div>
                    <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-2">{{ __('Belum Ada Kenangan') }}</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-6">{{ __('Mulai dokumentasikan momen-momen berharga ' . ($child->nickname ?? $child->name) . '.') }}</p>
                    <a href="{{ route('timeline.create', $child) }}" class="btn-primary">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        {{ __('Tambah Kenangan Pertama') }}
                    </a>
                </div>
            @else
                <!-- Timeline -->
                <div class="relative">
                    <!-- Timeline Line -->
                    <div class="absolute left-8 top-0 bottom-0 w-0.5 bg-lavender-200 dark:bg-lavender-800 hidden sm:block"></div>

                    <div class="space-y-6">
                        @foreach ($timelines as $item)
                            <div class="relative flex items-start gap-4 sm:gap-8">
                                <!-- Timeline Dot -->
                                <div class="hidden sm:flex shrink-0 w-16 justify-center">
                                    <div class="w-4 h-4 rounded-full {{ $item->is_featured ? 'bg-warmYellow-400 ring-4 ring-warmYellow-100 dark:ring-warmYellow-900/30' : 'bg-lavender-300 dark:bg-lavender-500' }} z-10"></div>
                                </div>

                                <!-- Timeline Card -->
                                <a href="{{ route('timeline.show', [$child, $item]) }}" class="card-hover block flex-1">
                                    <div class="flex items-start gap-4">
                                        <!-- Date Badge -->
                                        <div class="shrink-0 text-center">
                                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-lavender-50 to-softPink-50 dark:from-lavender-950/30 dark:to-softPink-950/30 flex flex-col items-center justify-center shadow-soft">
                                                <span class="text-xs font-medium text-lavender-500 dark:text-lavender-400">{{ $item->event_date->locale('id')->isoFormat('MMM') }}</span>
                                                <span class="text-lg font-bold text-gray-700 dark:text-gray-300">{{ $item->event_date->format('d') }}</span>
                                            </div>
                                        </div>

                                        <!-- Content -->
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-start justify-between gap-2">
                                                <h3 class="font-semibold text-gray-800 dark:text-gray-100">{{ $item->title }}</h3>
                                                @if ($item->is_featured)
                                                    <span class="shrink-0 text-xs px-2 py-0.5 rounded-full bg-warmYellow-100 dark:bg-warmYellow-950/30 text-warmYellow-600 dark:text-warmYellow-400">⭐ Unggulan</span>
                                                @endif
                                            </div>

                                            @if ($item->description)
                                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 line-clamp-2">{{ $item->description }}</p>
                                            @endif

                                            <div class="mt-2 flex flex-wrap items-center gap-3 text-xs text-gray-400 dark:text-gray-500">
                                                @if ($item->event_time)
                                                    <span>🕐 {{ $item->event_time }}</span>
                                                @endif
                                                @if ($item->location)
                                                    <span>📍 {{ $item->location }}</span>
                                                @endif
                                                @if ($item->mood)
                                                    <span>{{ $item->mood_label }}</span>
                                                @endif
                                                @if ($item->media->count() > 0)
                                                    <span>📎 {{ $item->media->count() }} media</span>
                                                @endif
                                            </div>

                                            @if ($item->tags && count($item->tags) > 0)
                                                <div class="mt-2 flex flex-wrap gap-1">
                                                    @foreach ($item->tags as $tag)
                                                        <span class="px-2 py-0.5 text-xs rounded-full bg-skyBlue-50 dark:bg-skyBlue-950/30 text-skyBlue-500 dark:text-skyBlue-400">#{{ $tag }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $timelines->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
