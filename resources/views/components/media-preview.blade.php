@props([
    'media' => null,
    'src' => null,
    'type' => 'photo',
    'name' => '',
    'size' => '',
    'typeLabel' => '',
])

@php
    $mediaSrc = $src ?? ($media ? asset('storage/' . $media->file_path) : '');
    $mediaType = $type ?? ($media ? $media->file_type : 'photo');
    $mediaName = $name ?? ($media ? $media->file_name : '');
    $mediaSize = $size ?? ($media ? $media->formatted_size : '');
    $mediaTypeLabel = $typeLabel ?? ($media ? $media->file_type_label : '');
    $isImage = $mediaType === 'photo';
    $isVideo = $mediaType === 'video';
    $isAudio = $mediaType === 'audio';
    $isPdf = $mediaType === 'document';
@endphp

@if ($mediaSrc)
    {{-- Image: clickable with lightbox --}}
    @if ($isImage)
        <div class="relative group rounded-2xl overflow-hidden bg-gradient-to-br from-lavender-50 to-softPink-50 dark:from-lavender-950/30 dark:to-softPink-950/30 shadow-soft aspect-square cursor-pointer"
             x-data="{
                 lightboxOpen: false,
                 open() { this.lightboxOpen = true; document.body.classList.add('overflow-hidden'); },
                 close() { this.lightboxOpen = false; document.body.classList.remove('overflow-hidden'); }
             }"
             @click="open()"
        >
            <img src="{{ $mediaSrc }}" alt="{{ $media->alt_text ?? $mediaName }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy" />

            {{-- Hover Overlay --}}
            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-all duration-300 flex items-center justify-center">
                <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center gap-2">
                    <span class="bg-white/90 dark:bg-gray-800/90 rounded-full p-2.5 shadow-lg">
                        <svg class="w-5 h-5 text-gray-700 dark:text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                        </svg>
                    </span>
                </div>
            </div>

            {{-- Mobile: tap indicator --}}
            <div class="absolute bottom-2 right-2 sm:hidden bg-black/50 rounded-full p-1.5">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                </svg>
            </div>

            {{-- Bottom info --}}
            <div class="absolute bottom-0 left-0 right-0 p-3 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 sm:group-hover:opacity-100 transition-opacity duration-300">
                <p class="text-xs font-medium text-white truncate">{{ $mediaName }}</p>
                <div class="flex items-center gap-2 mt-1">
                    <p class="text-xs text-white/75">{{ $mediaTypeLabel }} · {{ $mediaSize }}</p>
                </div>
            </div>

            {{-- Lightbox --}}
            <div
                x-show="lightboxOpen"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/85 backdrop-blur-sm p-4"
                @click.self="close()"
                @keydown.escape.window="close()"
                style="display: none;"
            >
                <div class="relative max-w-full max-h-full flex flex-col items-center" @click.stop>
                    <button type="button" @click="close()" class="absolute -top-12 right-0 sm:top-0 sm:right-0 text-white hover:text-gray-300 transition p-2 min-h-[44px] min-w-[44px] inline-flex items-center justify-center z-10">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <p class="text-white text-sm mb-3 text-center truncate max-w-[80vw]">{{ $mediaName }}</p>
                    <img src="{{ $mediaSrc }}" alt="{{ $media->alt_text ?? $mediaName }}" class="max-w-full max-h-[75vh] object-contain rounded-2xl shadow-2xl" />
                    <div class="flex items-center gap-3 mt-3">
                        <a href="{{ $mediaSrc }}" download="{{ $mediaName }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white/10 hover:bg-white/20 text-white text-sm rounded-xl transition min-h-[44px]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            {{ __('Download') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

    {{-- Video: clickable with lightbox --}}
    @elseif ($isVideo)
        <div class="relative group rounded-2xl overflow-hidden bg-gradient-to-br from-lavender-50 to-softPink-50 dark:from-lavender-950/30 dark:to-softPink-950/30 shadow-soft aspect-square cursor-pointer"
             x-data="{
                 lightboxOpen: false,
                 open() { this.lightboxOpen = true; document.body.classList.add('overflow-hidden'); },
                 close() { this.lightboxOpen = false; document.body.classList.remove('overflow-hidden'); }
             }"
             @click="open()"
        >
            <video src="{{ $mediaSrc }}" class="absolute inset-0 w-full h-full object-cover" preload="metadata" muted></video>

            {{-- Play button overlay --}}
            <div class="absolute inset-0 flex items-center justify-center bg-black/20 group-hover:bg-black/40 transition-all duration-300">
                <div class="w-14 h-14 rounded-full bg-white/90 dark:bg-gray-800/90 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-7 h-7 text-gray-700 dark:text-gray-200 ml-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                </div>
            </div>

            {{-- Bottom info --}}
            <div class="absolute bottom-0 left-0 right-0 p-3 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                <p class="text-xs font-medium text-white truncate">{{ $mediaName }}</p>
                <div class="flex items-center gap-2 mt-1">
                    <p class="text-xs text-white/75">{{ $mediaTypeLabel }} · {{ $mediaSize }}</p>
                </div>
            </div>

            {{-- Lightbox --}}
            <div
                x-show="lightboxOpen"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/85 backdrop-blur-sm p-4"
                @click.self="close()"
                @keydown.escape.window="close()"
                style="display: none;"
            >
                <div class="relative max-w-full max-h-full flex flex-col items-center" @click.stop>
                    <button type="button" @click="close()" class="absolute -top-12 right-0 sm:top-0 sm:right-0 text-white hover:text-gray-300 transition p-2 min-h-[44px] min-w-[44px] inline-flex items-center justify-center z-10">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <p class="text-white text-sm mb-3 text-center truncate max-w-[80vw]">{{ $mediaName }}</p>
                    <video src="{{ $mediaSrc }}" controls autoplay class="max-w-full max-h-[75vh] rounded-2xl shadow-2xl"></video>
                    <div class="flex items-center gap-3 mt-3">
                        <a href="{{ $mediaSrc }}" download="{{ $mediaName }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white/10 hover:bg-white/20 text-white text-sm rounded-xl transition min-h-[44px]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            {{ __('Download') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

    {{-- Audio --}}
    @elseif ($isAudio)
        <div class="relative group rounded-2xl overflow-hidden bg-gradient-to-br from-peach-50 to-warmYellow-50 dark:from-peach-950/30 dark:to-warmYellow-950/30 shadow-soft aspect-square flex flex-col items-center justify-center p-4">
            <span class="text-4xl mb-2">🎵</span>
            <p class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-full text-center">{{ $mediaName }}</p>
            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $mediaSize }}</p>
            <audio src="{{ $mediaSrc }}" controls class="mt-3 w-full max-w-[200px] h-8"></audio>
            <a href="{{ $mediaSrc }}" download="{{ $mediaName }}" class="mt-2 inline-flex items-center gap-1 text-xs text-lavender-500 hover:text-lavender-600 dark:text-lavender-400 dark:hover:text-lavender-300 transition min-h-[44px]">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                {{ __('Download') }}
            </a>
        </div>

    {{-- Other files (PDF, document, etc.) --}}
    @else
        <div class="relative group rounded-2xl overflow-hidden bg-gradient-to-br from-skyBlue-50 to-lavender-50 dark:from-skyBlue-950/30 dark:to-lavender-950/30 shadow-soft aspect-square flex flex-col items-center justify-center p-4">
            @php
                $ext = pathinfo($mediaName, PATHINFO_EXTENSION);
                $extLower = strtolower($ext);
                $icon = '📄';
                $color = 'skyBlue';
                if ($extLower === 'pdf') { $icon = '📕'; $color = 'softPink'; }
                elseif (in_array($extLower, ['doc', 'docx'])) { $icon = '📘'; $color = 'skyBlue'; }
                elseif (in_array($extLower, ['xls', 'xlsx'])) { $icon = '📊'; $color = 'mintGreen'; }
            @endphp
            <span class="text-4xl mb-2">{{ $icon }}</span>
            <p class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-full text-center font-medium">{{ $mediaName }}</p>
            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $mediaSize }}</p>
            <div class="flex items-center gap-2 mt-3">
                @if ($extLower === 'pdf')
                    <a href="{{ $mediaSrc }}" target="_blank" class="inline-flex items-center gap-1 px-3 py-1.5 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-xs font-medium rounded-xl hover:bg-gray-50 dark:hover:bg-gray-600 transition min-h-[44px]">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        {{ __('Buka') }}
                    </a>
                @endif
                <a href="{{ $mediaSrc }}" download="{{ $mediaName }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-xs font-medium rounded-xl hover:bg-gray-50 dark:hover:bg-gray-600 transition min-h-[44px]">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    {{ __('Download') }}
                </a>
            </div>
        </div>
    @endif
@endif
