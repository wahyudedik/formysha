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
                            <p class="text-sm text-gray-400 dark:text-gray-500">{{ __('empty_states.no_media_attached') }}</p>
                        @else
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                @foreach ($timeline->media as $media)
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

                                @foreach ($timeline->media as $media)
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
                </div>
            </div>

            <!-- Share Section -->
            <div class="bg-white dark:bg-gray-800 shadow-soft sm:rounded-2xl p-4 sm:p-6" x-data="{
                shareUrl: '{{ url()->current() }}',
                shareTitle: '{{ addslashes($timeline->title) }} — ForMysha',
                shareText: '{{ addslashes($timeline->description ?? $timeline->title) }}',
                async shareNative() {
                    if (navigator.share) {
                        try {
                            await navigator.share({ title: this.shareTitle, text: this.shareText, url: this.shareUrl });
                        } catch (e) { /* user cancelled */ }
                    }
                },
                copyLink() {
                    const text = this.shareUrl;
                    const doCopy = () => {
                        this.copied = true;
                        setTimeout(() => this.copied = false, 2000);
                    };
                    if (navigator.clipboard) {
                        navigator.clipboard.writeText(text).then(doCopy);
                    } else {
                        const t = document.createElement('textarea');
                        t.value = text;
                        t.style.position = 'fixed';
                        t.style.left = '-9999px';
                        document.body.appendChild(t);
                        t.select();
                        document.execCommand('copy');
                        document.body.removeChild(t);
                        doCopy();
                    }
                },
                copied: false
            }">
                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">📤 {{ __('Bagikan Kenangan') }}</h4>
                <div class="flex flex-wrap gap-2">
                    <button type="button" x-on:click="shareNative()" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-medium rounded-xl text-sm hover:from-blue-600 hover:to-blue-700 transition min-h-[44px]">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        {{ __('Facebook') }}
                    </button>
                    <button type="button" x-on:click="window.open('https://wa.me/?text=' + encodeURIComponent(shareText + ' ' + shareUrl), '_blank')" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-gradient-to-r from-green-500 to-green-600 text-white font-medium rounded-xl text-sm hover:from-green-600 hover:to-green-700 transition min-h-[44px]">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        {{ __('WhatsApp') }}
                    </button>
                    <button type="button" x-on:click="window.open('https://twitter.com/intent/tweet?text=' + encodeURIComponent(shareTitle) + '&url=' + encodeURIComponent(shareUrl), '_blank')" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-gradient-to-r from-sky-400 to-sky-500 text-white font-medium rounded-xl text-sm hover:from-sky-500 hover:to-sky-600 transition min-h-[44px]">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        {{ __('Twitter') }}
                    </button>
                    <button type="button" x-on:click="copyLink()" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium rounded-xl text-sm hover:bg-gray-200 dark:hover:bg-gray-600 transition min-h-[44px]">
                        <template x-if="!copied">
                            <span>🔗 {{ __('Salin Link') }}</span>
                        </template>
                        <template x-if="copied">
                            <span>✅ {{ __('Tersalin!') }}</span>
                        </template>
                    </button>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                <a href="{{ route('timeline.index', $child) }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium rounded-xl text-sm hover:bg-gray-200 dark:hover:bg-gray-600 transition min-h-[44px]">
                    ← {{ __('Kembali ke Timeline') }}
                </a>

                <button type="button"
                    x-data
                    x-on:click.prevent="$dispatch('delete-confirm', 'delete-timeline-{{ $timeline->id }}')"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-1 px-4 py-2.5 text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-950/30 font-medium rounded-xl transition text-sm min-h-[44px]">
                    🗑️ {{ __('Hapus Kenangan') }}
                </button>

                <x-confirm-delete
                    id="delete-timeline-{{ $timeline->id }}"
                    title="{{ __('Hapus Kenangan') }}"
                    message="{{ __('Apakah Anda yakin ingin menghapus kenangan ini? Tindakan ini tidak dapat dibatalkan.') }}"
                    action="{{ route('timeline.destroy', [$child, $timeline]) }}"
                />
            </div>
        </div>
    </div>
</x-app-layout>
