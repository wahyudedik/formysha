<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('diaries.index', $child) }}" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                    📝 {{ $diary->title }}
                </h2>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('diaries.edit', [$child, $diary]) }}" class="btn-secondary text-sm min-h-[44px]">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    {{ __('Edit') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 p-4 bg-mintGreen-50 dark:bg-mintGreen-950/30 border border-mintGreen-200 dark:border-mintGreen-800 text-mintGreen-700 dark:text-mintGreen-400 rounded-xl" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Diary Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl">
                <!-- Header -->
                <div class="bg-gradient-to-r from-peach-50 to-warmYellow-50 dark:from-peach-950/30 dark:to-warmYellow-950/30 p-6 sm:p-8">
                    <div class="flex items-start justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $diary->title }}</h1>
                            <div class="mt-2 flex flex-wrap items-center gap-3 text-sm text-gray-500 dark:text-gray-400">
                                <span>📅 {{ $diary->formatted_date }}</span>
                                @if ($diary->mood)
                                    <span>{{ $diary->mood_label }}</span>
                                @endif
                                @if ($diary->weather)
                                    <span>{{ $diary->weather_label }}</span>
                                @endif
                                @if ($diary->is_private)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-400 text-xs">🔒 {{ __('Privat') }}</span>
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
                                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75" @click="showDeleteConfirm = false"></div>
                                    <div class="relative bg-white dark:bg-gray-800 rounded-3xl shadow-soft max-w-md w-full p-6 z-10">
                                        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-2">{{ __('Hapus Catatan?') }}</h3>
                                        <p class="text-gray-500 dark:text-gray-400 mb-6">{{ __('Tindakan ini tidak dapat dibatalkan.') }}</p>
                                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 justify-end">
                                            <button @click="showDeleteConfirm = false" class="btn-secondary min-h-[44px]">
                                                {{ __('Batal') }}
                                            </button>
                                            <form method="POST" action="{{ route('diaries.destroy', [$child, $diary]) }}" x-on:submit="showDeleteConfirm = false">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-red-500 text-white font-semibold text-sm rounded-xl hover:bg-red-600 transition min-h-[44px]">
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

                <!-- Content -->
                <div class="p-6 sm:p-8">
                    <div class="prose prose-gray dark:prose-invert max-w-none">
                        {!! nl2br(e($diary->content)) !!}
                    </div>
                </div>

                <!-- Media Section -->
                <div class="px-6 sm:px-8 py-6 border-t border-gray-100 dark:border-gray-700">
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">{{ __('Media / Lampiran') }}</h4>

                    {{-- Upload Form --}}
                    <form method="POST" action="{{ route('media.store.diary', [$child, $diary]) }}" enctype="multipart/form-data" class="mb-4">
                        @csrf
                        <x-media-upload name="media[]" :multiple="true" />
                        <button type="submit" class="btn-primary text-sm min-h-[44px]">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ __('Unggah Media') }}
                        </button>
                    </form>

                    @if ($diary->media->isNotEmpty())
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach ($diary->media as $media)
                                <div class="relative group rounded-2xl overflow-hidden bg-gradient-to-br from-lavender-50 to-softPink-50 dark:from-lavender-950/30 dark:to-softPink-950/30 shadow-soft aspect-square">
                                    @if ($media->file_type === 'photo')
                                        <img src="{{ asset('storage/' . $media->file_path) }}" alt="{{ $media->alt_text ?? $media->file_name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy" />
                                    @elseif ($media->file_type === 'video')
                                        <video src="{{ asset('storage/' . $media->file_path) }}" class="absolute inset-0 w-full h-full object-cover" preload="metadata" controls></video>
                                    @elseif ($media->file_type === 'audio')
                                        <div class="w-full h-full flex flex-col items-center justify-center p-3">
                                            <span class="text-4xl mb-2">🎵</span>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-full">{{ $media->file_name }}</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $media->formatted_size }}</p>
                                        </div>
                                    @else
                                        <div class="w-full h-full flex flex-col items-center justify-center p-3">
                                            <span class="text-4xl mb-2">📄</span>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-full">{{ $media->file_name }}</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $media->formatted_size }}</p>
                                        </div>
                                    @endif

                                    <!-- Hover Overlay -->
                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-300 flex items-end">
                                        <div class="w-full p-3 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                            <p class="text-xs font-medium truncate">{{ $media->file_name }}</p>
                                            <p class="text-xs opacity-75">{{ $media->file_type_label }} · {{ $media->formatted_size }}</p>
                                        </div>
                                    </div>

                                    <!-- Delete Button -->
                                    <button type="button" class="w-8 h-8 rounded-full bg-red-500 text-white flex items-center justify-center hover:bg-red-600 transition shadow-lg"
                                        x-data
                                        x-on:click.prevent="$dispatch('delete-confirm', 'delete-media-{{ $media->id }}')">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            @endforeach

                            @foreach ($diary->media as $media)
                                <x-confirm-delete
                                    id="delete-media-{{ $media->id }}"
                                    title="{{ __('Hapus Media') }}"
                                    message="{{ __('Apakah Anda yakin ingin menghapus media ini? Tindakan ini tidak dapat dibatalkan.') }}"
                                    action="{{ route('media.destroy', [$child, $media]) }}"
                                />
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Footer -->
                <div class="px-4 sm:px-6 lg:px-8 py-4 border-t border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 text-xs text-gray-400 dark:text-gray-500">
                    <span>{{ __('Ditulis oleh') }} {{ $diary->user->name }}</span>
                    <span>{{ __('Dibuat') }} {{ $diary->created_at->locale('id')->isoFormat('D MMMM YYYY, HH:mm') }}</span>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
