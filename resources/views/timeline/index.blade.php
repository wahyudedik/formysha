<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('children.show', $child) }}" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    📅 {{ __('Timeline') }} — {{ $child->nickname ?? $child->name }}
                </h2>
            </div>
            <a href="{{ route('timeline.create', $child) }}" class="btn-primary text-sm">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('Tambah Kenangan') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 p-4 bg-mintGreen-50 border border-mintGreen-200 text-mintGreen-700 rounded-xl" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)">
                    {{ session('status') }}
                </div>
            @endif

            @if ($timelines->isEmpty())
                <!-- Empty State -->
                <div class="text-center py-16">
                    <div class="text-6xl mb-4">📸</div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">{{ __('Belum Ada Kenangan') }}</h3>
                    <p class="text-gray-500 mb-6">{{ __('Mulai dokumentasikan momen-momen berharga ' . ($child->nickname ?? $child->name) . '.') }}</p>
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
                    <div class="absolute left-8 top-0 bottom-0 w-0.5 bg-lavender-200 hidden sm:block"></div>

                    <div class="space-y-6">
                        @foreach ($timelines as $item)
                            <div class="relative flex items-start gap-4 sm:gap-8">
                                <!-- Timeline Dot -->
                                <div class="hidden sm:flex shrink-0 w-16 justify-center">
                                    <div class="w-4 h-4 rounded-full {{ $item->is_featured ? 'bg-warmYellow-400 ring-4 ring-warmYellow-100' : 'bg-lavender-300' }} z-10"></div>
                                </div>

                                <!-- Timeline Card -->
                                <a href="{{ route('timeline.show', [$child, $item]) }}" class="card-hover block flex-1">
                                    <div class="flex items-start gap-4">
                                        <!-- Date Badge -->
                                        <div class="shrink-0 text-center">
                                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-lavender-50 to-softPink-50 flex flex-col items-center justify-center shadow-soft">
                                                <span class="text-xs font-medium text-lavender-500">{{ $item->event_date->locale('id')->isoFormat('MMM') }}</span>
                                                <span class="text-lg font-bold text-gray-700">{{ $item->event_date->format('d') }}</span>
                                            </div>
                                        </div>

                                        <!-- Content -->
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-start justify-between gap-2">
                                                <h3 class="font-semibold text-gray-800">{{ $item->title }}</h3>
                                                @if ($item->is_featured)
                                                    <span class="shrink-0 text-xs px-2 py-0.5 rounded-full bg-warmYellow-100 text-warmYellow-600">⭐ Unggulan</span>
                                                @endif
                                            </div>

                                            @if ($item->description)
                                                <p class="mt-1 text-sm text-gray-500 line-clamp-2">{{ $item->description }}</p>
                                            @endif

                                            <div class="mt-2 flex flex-wrap items-center gap-3 text-xs text-gray-400">
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
                                                        <span class="px-2 py-0.5 text-xs rounded-full bg-skyBlue-50 text-skyBlue-500">#{{ $tag }}</span>
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
