<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('children.show', $child) }}" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                    📝 {{ __('My Diary') }} — {{ $child->nickname ?? $child->name }}
                </h2>
            </div>
            <a href="{{ route('diaries.create', $child) }}" class="btn-primary text-sm min-h-[44px]">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('Tulis Diary') }}
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

            @if ($diaries->isEmpty())
                <!-- Empty State -->
                <div class="text-center py-16">
                    <div class="text-6xl mb-4">📖</div>
                    <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-2">{{ __('Belum Ada Catatan') }}</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-6">{{ __('Tulis cerita dan perkembangan harian ' . ($child->nickname ?? $child->name) . '.') }}</p>
                    <a href="{{ route('diaries.create', $child) }}" class="btn-primary min-h-[44px]">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        {{ __('Tulis Catatan Pertama') }}
                    </a>
                </div>
            @else
                <!-- Diary List -->
                <div class="space-y-4">
                    @foreach ($diaries as $entry)
                        <a href="{{ route('diaries.show', [$child, $entry]) }}" class="card-hover block">
                            <div class="flex items-start gap-4">
                                <!-- Date Badge -->
                                <div class="shrink-0 text-center">
                                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-peach-50 to-warmYellow-50 dark:from-peach-950/30 dark:to-warmYellow-950/30 flex flex-col items-center justify-center shadow-soft">
                                        <span class="text-xs font-medium text-peach-500 dark:text-peach-400">{{ $entry->diary_date->locale('id')->isoFormat('MMM') }}</span>
                                        <span class="text-lg font-bold text-gray-700 dark:text-gray-300">{{ $entry->diary_date->format('d') }}</span>
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-2">
                                        <h3 class="font-semibold text-gray-800 dark:text-gray-100">{{ $entry->title }}</h3>
                                        <div class="flex items-center gap-2 shrink-0">
                                            @if ($entry->is_private)
                                                <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">🔒</span>
                                            @else
                                                <span class="text-xs px-2 py-0.5 rounded-full bg-mintGreen-100 dark:bg-mintGreen-950/30 text-mintGreen-600 dark:text-mintGreen-400">🌐</span>
                                            @endif
                                        </div>
                                    </div>

                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 line-clamp-2">{{ Str::limit($entry->content, 150) }}</p>

                                    <div class="mt-2 flex flex-wrap items-center gap-3 text-xs text-gray-400 dark:text-gray-500">
                                        @if ($entry->mood)
                                            <span>{{ $entry->mood_label }}</span>
                                        @endif
                                        @if ($entry->weather)
                                            <span>{{ $entry->weather_label }}</span>
                                        @endif
                                        <span>📅 {{ $entry->formatted_date }}</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $diaries->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
