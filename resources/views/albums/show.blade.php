<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <a href="{{ route('albums.index', $child) }}" class="shrink-0 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight truncate">
                    📁 {{ $album->name }}
                </h2>
            </div>
            <a href="{{ route('albums.edit', [$child, $album]) }}" class="shrink-0 inline-flex items-center justify-center gap-1 px-4 py-2.5 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-xl text-sm hover:bg-gray-50 dark:hover:bg-gray-600 transition min-h-[44px]">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                {{ __('Edit') }}
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

            <!-- Album Header Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl mb-8">
                <div class="p-6 sm:p-8">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $album->name }}</h1>
                            @if ($album->description)
                                <p class="mt-2 text-gray-500 dark:text-gray-400">{{ $album->description }}</p>
                            @endif
                            <div class="mt-3 flex flex-wrap items-center gap-3 text-sm text-gray-400 dark:text-gray-500">
                                <span>📎 {{ $album->media_count ?? $album->media->count() }} {{ __('media') }}</span>
                                <span>📅 {{ $album->created_at->locale('id')->isoFormat('D MMMM YYYY') }}</span>
                                @if ($album->is_private)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-xs">🔒 {{ __('Privat') }}</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-mintGreen-100 dark:bg-mintGreen-950/30 text-mintGreen-600 dark:text-mintGreen-400 text-xs">🌐 {{ __('Publik') }}</span>
                                @endif
                            </div>
                        </div>

                        <!-- Delete Button -->
                        <div x-data="{ showDeleteConfirm: false }">
                            <button @click="showDeleteConfirm = true" class="text-gray-400 hover:text-red-500 dark:text-gray-500 dark:hover:text-red-400 transition p-2 rounded-xl hover:bg-red-50 dark:hover:bg-red-950/30">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>

                            <!-- Delete Confirmation Modal -->
                            <div x-show="showDeleteConfirm" x-cloak class="fixed inset-0 z-50 overflow-y-auto" x-transition>
                                <div class="flex items-center justify-center min-h-screen px-4">
                                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 dark:bg-gray-950 dark:bg-opacity-75" @click="showDeleteConfirm = false"></div>
                                    <div class="relative bg-white dark:bg-gray-800 rounded-3xl shadow-soft max-w-md w-full p-6 z-10">
                                        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-2">{{ __('Hapus Album?') }}</h3>
                                        <p class="text-gray-500 dark:text-gray-400 mb-6">{{ __('Semua media dalam album ini akan dihapus. Tindakan ini tidak dapat dibatalkan.') }}</p>
                                        <div class="flex items-center gap-3 justify-end">
                                            <button @click="showDeleteConfirm = false" class="btn-secondary">
                                                {{ __('Batal') }}
                                            </button>
                                            <form method="POST" action="{{ route('albums.destroy', [$child, $album]) }}" x-on:submit="showDeleteConfirm = false">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-500 text-white font-semibold text-sm rounded-xl hover:bg-red-600 transition">
                                                    {{ __('Hapus') }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upload Media -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl mb-8">
                <div class="p-6">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-100 mb-4">📷 {{ __('Tambah Media') }}</h3>
                    <form method="POST" action="{{ route('media.store.album', [$child, $album]) }}" enctype="multipart/form-data">
                        @csrf
                        <x-media-upload name="media[]" :multiple="true" />
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                            <button type="submit" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-softPink-500 hover:bg-softPink-600 text-white font-medium rounded-xl text-sm shadow-soft transition-all duration-200 min-h-[44px]">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ __('Unggah Media') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Media Grid -->
            @if ($album->media->isEmpty())
                <div class="text-center py-16">
                    <div class="text-5xl mb-4">📸</div>
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-2">{{ __('Belum Ada Media') }}</h3>
                    <p class="text-gray-500 dark:text-gray-400">{{ __('Mulai tambahkan foto dan video ke album ini.') }}</p>
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                    @foreach ($album->media as $item)
                        <div class="group relative aspect-square rounded-2xl overflow-hidden bg-gradient-to-br from-lavender-50 to-softPink-50 dark:from-lavender-950/30 dark:to-softPink-950/30 shadow-soft">
                            @if ($item->file_type === 'photo')
                                <img src="{{ asset('storage/' . $item->file_path) }}" alt="{{ $item->alt_text ?? $item->file_name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                            @elseif ($item->file_type === 'video')
                                <div class="w-full h-full flex flex-col items-center justify-center">
                                    <span class="text-3xl mb-1">🎬</span>
                                    <span class="text-xs text-gray-400 dark:text-gray-500">{{ $item->file_name }}</span>
                                </div>
                            @elseif ($item->file_type === 'audio')
                                <div class="w-full h-full flex flex-col items-center justify-center">
                                    <span class="text-3xl mb-1">🎵</span>
                                    <span class="text-xs text-gray-400 dark:text-gray-500">{{ $item->file_name }}</span>
                                </div>
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center">
                                    <span class="text-3xl mb-1">📄</span>
                                    <span class="text-xs text-gray-400 dark:text-gray-500">{{ $item->file_name }}</span>
                                </div>
                            @endif

                            <!-- Hover Overlay -->
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-300 flex items-end">
                                <div class="w-full p-3 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    <p class="text-xs font-medium truncate">{{ $item->file_name }}</p>
                                    <p class="text-xs opacity-75">{{ $item->file_type_label }} · {{ $item->formatted_size }}</p>
                                </div>
                            </div>

                            <!-- Delete Button -->
                            <form method="POST" action="{{ route('media.destroy', [$child, $item]) }}" class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition" onsubmit="return confirm('{{ __('Yakin ingin menghapus media ini?') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-8 h-8 rounded-full bg-red-500 text-white flex items-center justify-center hover:bg-red-600 transition shadow-lg">
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
</x-app-layout>
