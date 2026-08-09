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

            @if ($diaries->isEmpty() && !$request->hasAny(['date_from', 'date_to', 'mood']))
                <!-- Empty State -->
                <div class="text-center py-16">
                    <div class="w-24 h-24 mx-auto mb-6 rounded-3xl bg-gradient-to-br from-peach-50 to-warmYellow-50 dark:from-peach-950/30 dark:to-warmYellow-950/30 flex items-center justify-center">
                        <span class="text-5xl">📖</span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-2">{{ __('Belum Ada Catatan') }}</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-6 max-w-md mx-auto">{{ __('Tulis cerita dan perkembangan harian ' . ($child->nickname ?? $child->name) . '. Setiap hari punya cerita unik.') }}</p>
                    <a href="{{ route('diaries.create', $child) }}" class="btn-primary min-h-[44px]">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        {{ __('Tulis Catatan Pertama') }}
                    </a>
                </div>
            @else
                <!-- Filter & Sort Bar -->
                <div class="mb-6 flex flex-col sm:flex-row gap-3" x-data="{ showFilters: false }">
                    <div class="flex items-center gap-2">
                        <button type="button" @click="showFilters = !showFilters" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-xl text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition min-h-[44px]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            {{ __('Filter') }}
                        </button>
                        <select onchange="window.location.href=this.value" class="px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-xl text-sm font-medium text-gray-600 dark:text-gray-300 min-h-[44px] focus:ring-2 focus:ring-peach-300 dark:focus:ring-peach-600 focus:border-peach-400 dark:focus:border-peach-500">
                            <option value="{{ route('diaries.index', array_merge(['child' => $child], ['sort' => 'newest'] + request()->except(['sort', 'page']))) }}" {{ $currentSort === 'newest' ? 'selected' : '' }}>🕐 {{ __('Terbaru') }}</option>
                            <option value="{{ route('diaries.index', array_merge(['child' => $child], ['sort' => 'oldest'] + request()->except(['sort', 'page']))) }}" {{ $currentSort === 'oldest' ? 'selected' : '' }}>🕐 {{ __('Terlama') }}</option>
                            <option value="{{ route('diaries.index', array_merge(['child' => $child], ['sort' => 'title_asc'] + request()->except(['sort', 'page']))) }}" {{ $currentSort === 'title_asc' ? 'selected' : '' }}>🔤 {{ __('Judul A-Z') }}</option>
                            <option value="{{ route('diaries.index', array_merge(['child' => $child], ['sort' => 'title_desc'] + request()->except(['sort', 'page']))) }}" {{ $currentSort === 'title_desc' ? 'selected' : '' }}>🔤 {{ __('Judul Z-A') }}</option>
                        </select>
                    </div>

                    @if ($diaries->hasPages() || $diaries->total() > 0)
                        <div class="text-sm text-gray-400 dark:text-gray-500 flex items-center">
                            {{ $diaries->total() }} {{ __('catatan') }}
                        </div>
                    @endif
                </div>

                <!-- Filter Panel -->
                <div x-show="showFilters" x-transition x-cloak class="mb-6 p-4 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-soft">
                    <form action="{{ route('diaries.index', $child) }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                        <input type="hidden" name="sort" value="{{ $currentSort }}">
                        <div class="flex-1">
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('Dari Tanggal') }}</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-peach-300 dark:focus:ring-peach-600 focus:border-peach-400 dark:focus:border-peach-500 min-h-[44px]">
                        </div>
                        <div class="flex-1">
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('Sampai Tanggal') }}</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-peach-300 dark:focus:ring-peach-600 focus:border-peach-400 dark:focus:border-peach-500 min-h-[44px]">
                        </div>
                        <div class="flex-1">
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('Mood') }}</label>
                            <select name="mood" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-peach-300 dark:focus:ring-peach-600 focus:border-peach-400 dark:focus:border-peach-500 min-h-[44px]">
                                <option value="">{{ __('Semua Mood') }}</option>
                                <option value="happy" {{ request('mood') === 'happy' ? 'selected' : '' }}>😊 Bahagia</option>
                                <option value="excited" {{ request('mood') === 'excited' ? 'selected' : '' }}>🤩 Bersemangat</option>
                                <option value="calm" {{ request('mood') === 'calm' ? 'selected' : '' }}>😌 Tenang</option>
                                <option value="sad" {{ request('mood') === 'sad' ? 'selected' : '' }}>😢 Sedih</option>
                                <option value="tired" {{ request('mood') === 'tired' ? 'selected' : '' }}>😴 Lelah</option>
                                <option value="grateful" {{ request('mood') === 'grateful' ? 'selected' : '' }}>🙏 Bersyukur</option>
                            </select>
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="submit" class="px-5 py-2.5 bg-peach-500 hover:bg-peach-600 text-white font-medium rounded-xl text-sm transition min-h-[44px]">
                                {{ __('Terapkan') }}
                            </button>
                            @if (request()->hasAny(['date_from', 'date_to', 'mood']))
                                <a href="{{ route('diaries.index', ['child' => $child, 'sort' => $currentSort]) }}" class="px-5 py-2.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 font-medium rounded-xl text-sm transition min-h-[44px]">
                                    {{ __('Reset') }}
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

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
