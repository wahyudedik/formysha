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
                    📁 {{ __('My Gallery') }} — {{ $child->nickname ?? $child->name }}
                </h2>
            </div>
            <a href="{{ route('albums.create', $child) }}" class="btn-primary text-sm">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('Tambah Album') }}
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

            @if ($albums->isEmpty())
                <!-- Empty State -->
                <div class="text-center py-16">
                    <div class="text-6xl mb-4">📷</div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">{{ __('Belum Ada Album') }}</h3>
                    <p class="text-gray-500 mb-6">{{ __('Kumpulkan foto dan video terbaik ' . ($child->nickname ?? $child->name) . ' dalam album.') }}</p>
                    <a href="{{ route('albums.create', $child) }}" class="btn-primary">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        {{ __('Buat Album Pertama') }}
                    </a>
                </div>
            @else
                <!-- Album Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach ($albums as $album)
                        <a href="{{ route('albums.show', [$child, $album]) }}" class="card-hover block">
                            <!-- Cover Photo -->
                            <div class="aspect-square rounded-2xl overflow-hidden bg-gradient-to-br from-lavender-50 to-softPink-50 mb-4">
                                @if ($album->cover_photo)
                                    <img src="{{ asset('storage/' . $album->cover_photo) }}" alt="{{ $album->name }}" class="w-full h-full object-cover" />
                                @else
                                    <div class="w-full h-full flex flex-col items-center justify-center">
                                        <span class="text-4xl mb-2">📸</span>
                                        <span class="text-sm text-gray-400">{{ $album->media_count ?? 0 }} {{ __('foto') }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Album Info -->
                            <div class="px-1">
                                <div class="flex items-start justify-between gap-2">
                                    <h3 class="font-semibold text-gray-800 truncate">{{ $album->name }}</h3>
                                    @if ($album->is_private)
                                        <span class="shrink-0 text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-500">🔒</span>
                                    @else
                                        <span class="shrink-0 text-xs px-2 py-0.5 rounded-full bg-mintGreen-100 text-mintGreen-600">🌐</span>
                                    @endif
                                </div>

                                @if ($album->description)
                                    <p class="mt-1 text-sm text-gray-500 line-clamp-2">{{ $album->description }}</p>
                                @endif

                                <div class="mt-2 flex items-center gap-3 text-xs text-gray-400">
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
