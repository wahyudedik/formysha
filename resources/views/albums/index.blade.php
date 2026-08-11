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
                    📁 {{ __('Galeri') }} — {{ $child->nickname ?? $child->name }}
                </h2>
            </div>
            <a href="{{ route('albums.create', $child) }}" class="shrink-0 inline-flex items-center justify-center gap-1 px-4 py-2.5 bg-softPink-500 hover:bg-softPink-600 text-white font-medium rounded-xl text-sm shadow-soft transition min-h-[44px]">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('Tambah Album') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 p-4 bg-mintGreen-50 border border-mintGreen-200 text-mintGreen-700 dark:bg-mintGreen-950/30 dark:border-mintGreen-800 dark:text-mintGreen-400 rounded-xl" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)">
                    {{ session('status') }}
                </div>
            @endif

            @if ($albums->isEmpty())
                <!-- Empty State -->
                <div class="text-center py-16">
                    <div class="w-24 h-24 mx-auto mb-6 rounded-3xl bg-gradient-to-br from-lavender-50 to-softPink-50 dark:from-lavender-950/30 dark:to-softPink-950/30 flex items-center justify-center">
                        <span class="text-5xl">📷</span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-2">{{ __('Belum Ada Album') }}</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-6 max-w-md mx-auto">{{ __('Kumpulkan foto dan video terbaik ' . ($child->nickname ?? $child->name) . ' dalam album yang rapi dan terorganisir.') }}</p>
                    <a href="{{ route('albums.create', $child) }}" class="btn-primary min-h-[44px]">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        {{ __('Buat Album Pertama') }}
                    </a>
                </div>
            @else
                <!-- Sort Bar -->
                <div class="mb-6 flex flex-col sm:flex-row gap-3">
                    <div class="flex items-center gap-2">
                        <select onchange="window.location.href=this.value" class="px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-xl text-sm font-medium text-gray-600 dark:text-gray-300 min-h-[44px] focus:ring-2 focus:ring-lavender-300 dark:focus:ring-lavender-600 focus:border-lavender-400 dark:focus:border-lavender-500">
                            <option value="{{ route('albums.index', array_merge(['child' => $child], ['sort' => 'default'] + request()->except(['sort', 'page']))) }}" {{ $currentSort === 'default' ? 'selected' : '' }}>⭐ {{ __('Default') }}</option>
                            <option value="{{ route('albums.index', array_merge(['child' => $child], ['sort' => 'newest'] + request()->except(['sort', 'page']))) }}" {{ $currentSort === 'newest' ? 'selected' : '' }}>🕐 {{ __('Terbaru') }}</option>
                            <option value="{{ route('albums.index', array_merge(['child' => $child], ['sort' => 'oldest'] + request()->except(['sort', 'page']))) }}" {{ $currentSort === 'oldest' ? 'selected' : '' }}>🕐 {{ __('Terlama') }}</option>
                            <option value="{{ route('albums.index', array_merge(['child' => $child], ['sort' => 'name_asc'] + request()->except(['sort', 'page']))) }}" {{ $currentSort === 'name_asc' ? 'selected' : '' }}>🔤 {{ __('Nama A-Z') }}</option>
                            <option value="{{ route('albums.index', array_merge(['child' => $child], ['sort' => 'name_desc'] + request()->except(['sort', 'page']))) }}" {{ $currentSort === 'name_desc' ? 'selected' : '' }}>🔤 {{ __('Nama Z-A') }}</option>
                            <option value="{{ route('albums.index', array_merge(['child' => $child], ['sort' => 'most_media'] + request()->except(['sort', 'page']))) }}" {{ $currentSort === 'most_media' ? 'selected' : '' }}>📎 {{ __('Paling Banyak Media') }}</option>
                        </select>
                    </div>

                    @if ($albums->hasPages() || $albums->total() > 0)
                        <div class="text-sm text-gray-400 dark:text-gray-500 flex items-center">
                            {{ $albums->total() }} {{ __('album') }}
                        </div>
                    @endif
                </div>

                <!-- Album Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach ($albums as $album)
                        <a href="{{ route('albums.show', [$child, $album]) }}" class="card-hover block">
                            <!-- Cover Photo / Grid Preview -->
                            <div class="aspect-square rounded-2xl overflow-hidden bg-gradient-to-br from-lavender-50 to-softPink-50 dark:from-lavender-950/30 dark:to-softPink-950/30 mb-4">
                                @if ($album->cover_photo)
                                    <img src="{{ asset('storage/' . $album->cover_photo) }}" alt="{{ $album->name }}" class="w-full h-full object-cover" loading="lazy" />
                                @elseif ($album->media->isNotEmpty())
                                    {{-- Grid 2x2 photo preview --}}
                                    <div class="w-full h-full grid grid-cols-2 grid-rows-2 gap-0.5">
                                        @foreach ($album->media as $item)
                                            <div class="overflow-hidden bg-gray-100 dark:bg-gray-700">
                                                @if ($item->thumbnail_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($item->thumbnail_path))
                                                    <img src="{{ asset('storage/' . $item->thumbnail_path) }}" alt="" class="w-full h-full object-cover" loading="lazy" />
                                                @elseif (\Illuminate\Support\Facades\Storage::disk('public')->exists($item->file_path))
                                                    <img src="{{ asset('storage/' . $item->file_path) }}" alt="" class="w-full h-full object-cover" loading="lazy" />
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center">
                                                        <span class="text-lg">📸</span>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="w-full h-full flex flex-col items-center justify-center">
                                        <span class="text-4xl mb-2">📸</span>
                                        <span class="text-sm text-gray-400 dark:text-gray-500">{{ $album->media_count ?? 0 }} {{ __('foto') }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Album Info -->
                            <div class="px-1">
                                <div class="flex items-start justify-between gap-2">
                                    <h3 class="font-semibold text-gray-800 dark:text-gray-100 truncate">{{ $album->name }}</h3>
                                    @if ($album->is_private)
                                        <span class="shrink-0 text-xs px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">🔒</span>
                                    @else
                                        <span class="shrink-0 text-xs px-2 py-0.5 rounded-full bg-mintGreen-100 dark:bg-mintGreen-950/30 text-mintGreen-600 dark:text-mintGreen-400">🌐</span>
                                    @endif
                                </div>

                                @if ($album->description)
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 line-clamp-2">{{ $album->description }}</p>
                                @endif

                                <div class="mt-2 flex items-center gap-3 text-xs text-gray-400 dark:text-gray-500">
                                    <span>📎 {{ $album->media_count ?? 0 }} {{ __('media') }}</span>
                                    <span>📅 {{ $album->created_at->locale('id')->isoFormat('D MMM YYYY') }}</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $albums->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
