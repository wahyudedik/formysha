@props([
    'name' => 'media[]',
    'multiple' => true,
    'maxFiles' => 10,
    'accept' => 'image/*,video/*,audio/*',
    'maxSize' => '10MB',
    'existingMedia' => [],
])

<div
    x-data="{
        files: [],
        previews: [],
        isDragging: false,
        maxFiles: {{ $maxFiles }},
        maxSizeBytes: 10485760,
        existingMedia: {{ Js::from($existingMedia) }},
        syncFileInput() {
            const dt = new DataTransfer();
            this.files.forEach(file => dt.items.add(file));
            this.$refs.fileInput.files = dt.files;
        },
        removeFile(index) {
            if (this.previews[index]) {
                URL.revokeObjectURL(this.previews[index]);
            }
            this.files.splice(index, 1);
            this.previews.splice(index, 1);
            this.syncFileInput();
        },
        removeExisting(index) {
            this.existingMedia.splice(index, 1);
        },
        formatSize(bytes) {
            if (bytes >= 1073741824) return (bytes / 1073741824).toFixed(2) + ' GB';
            if (bytes >= 1048576) return (bytes / 1048576).toFixed(2) + ' MB';
            if (bytes >= 1024) return (bytes / 1024).toFixed(2) + ' KB';
            return bytes + ' B';
        },
        getFileCategory(type) {
            if (type.startsWith('image/')) return 'image';
            if (type.startsWith('video/')) return 'video';
            if (type.startsWith('audio/')) return 'audio';
            if (type === 'application/pdf') return 'pdf';
            return 'file';
        },
        getFileIcon(type) {
            const cat = this.getFileCategory(type);
            if (cat === 'image') return '🖼️';
            if (cat === 'video') return '🎬';
            if (cat === 'audio') return '🎵';
            if (cat === 'pdf') return '📕';
            return '📄';
        },
        handleDrop(e) {
            e.preventDefault();
            this.isDragging = false;
            const droppedFiles = Array.from(e.dataTransfer.files);
            this.addFiles(droppedFiles);
        },
        handleFileSelect(e) {
            const selectedFiles = Array.from(e.target.files);
            e.target.value = '';
            this.addFiles(selectedFiles);
        },
        addFiles(newFiles) {
            const remaining = this.maxFiles - this.files.length;
            const filesToAdd = newFiles.slice(0, remaining);
            for (const file of filesToAdd) {
                if (file.size > this.maxSizeBytes) {
                    alert('File ' + file.name + ' melebihi batas maksimum {{ $maxSize }}.');
                    continue;
                }
                this.files.push(file);
                const cat = this.getFileCategory(file.type);
                if (cat === 'image' || cat === 'video') {
                    this.previews.push(URL.createObjectURL(file));
                } else {
                    this.previews.push(null);
                }
            }
            this.syncFileInput();
        },
        lightboxOpen: false,
        lightboxSrc: '',
        lightboxType: '',
        lightboxName: '',
        openLightbox(src, type, name) {
            this.lightboxSrc = src;
            this.lightboxType = type;
            this.lightboxName = name;
            this.lightboxOpen = true;
            document.body.classList.add('overflow-hidden');
        },
        closeLightbox() {
            this.lightboxOpen = false;
            this.lightboxSrc = '';
            document.body.classList.remove('overflow-hidden');
        }
    }"
    class="mb-5"
>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
        {{ __('Media (foto, video, audio)') }}
        <span class="text-gray-400 dark:text-gray-500 font-normal">— opsional, maks {{ $maxSize }}</span>
    </label>

    {{-- Drop Zone --}}
    <div
        class="mt-1 flex flex-col items-center justify-center px-6 py-8 border-2 border-dashed rounded-2xl transition cursor-pointer"
        :class="isDragging ? 'border-lavender-400 bg-lavender-50 dark:bg-lavender-950/30' : 'border-gray-300 dark:border-gray-600 hover:border-lavender-400 bg-gradient-to-br from-lavender-50/50 to-softPink-50/50 dark:from-lavender-950/20 dark:to-softPink-950/20'"
        @dragover.prevent="isDragging = true"
        @dragleave.prevent="isDragging = false"
        @drop="handleDrop($event)"
        @click="$refs.fileInput.click()"
    >
        <svg class="w-10 h-10 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        <p class="text-sm text-gray-500 dark:text-gray-400 text-center">
            {{ __('Seret file ke sini atau') }}
            <span class="font-medium text-lavender-600">{{ __('pilih file') }}</span>
        </p>
        <p class="text-xs text-gray-400 mt-1">
            JPG, PNG, GIF, WebP, MP4, MOV, WebM, MP3, WAV (Maks. {{ $maxSize }})
        </p>
    </div>

    <input
        type="file"
        name="{{ $name }}"
        x-ref="fileInput"
        class="sr-only"
        {{ $multiple ? 'multiple' : '' }}
        accept="{{ $accept }}"
        @change="handleFileSelect($event)"
    >

    {{-- New Files Preview --}}
    <template x-if="files.length > 0">
        <div class="mt-3 space-y-2">
            <p class="text-xs text-gray-400 dark:text-gray-500 font-medium">📎 {{ __('File yang dipilih:') }} <span class="text-mintGreen-500" x-text="files.length + ' file'"></span></p>
            <template x-for="(file, index) in files" :key="index">
                <div class="flex items-center gap-3 p-3 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl group">
                    {{-- Thumbnail Preview --}}
                    <template x-if="previews[index] && getFileCategory(file.type) === 'image'">
                        <button type="button" @click.stop="openLightbox(previews[index], 'image', file.name)" class="w-14 h-14 rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-600 shrink-0 ring-2 ring-mintGreen-200 dark:ring-mintGreen-800">
                            <img :src="previews[index]" :alt="file.name" class="w-full h-full object-cover" />
                        </button>
                    </template>
                    <template x-if="previews[index] && getFileCategory(file.type) === 'video'">
                        <div class="w-14 h-14 rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-600 shrink-0 ring-2 ring-mintGreen-200 dark:ring-mintGreen-800 relative">
                            <video :src="previews[index]" class="w-full h-full object-cover" muted preload="metadata"></video>
                            <div class="absolute inset-0 flex items-center justify-center bg-black/30">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            </div>
                        </div>
                    </template>
                    <template x-if="!previews[index]">
                        <div class="w-14 h-14 rounded-xl bg-lavender-50 dark:bg-lavender-950/30 flex items-center justify-center shrink-0 ring-2 ring-mintGreen-200 dark:ring-mintGreen-800">
                            <span class="text-2xl" x-text="getFileIcon(file.type)"></span>
                        </div>
                    </template>

                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-200 truncate" x-text="file.name"></p>
                        <div class="flex items-center gap-2">
                            <p class="text-xs text-gray-400 dark:text-gray-500" x-text="formatSize(file.size)"></p>
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full bg-mintGreen-100 dark:bg-mintGreen-950/30 text-mintGreen-600 dark:text-mintGreen-400 text-[10px] font-medium">
                                ✓ {{ __('Siap diunggah') }}
                            </span>
                        </div>
                    </div>

                    {{-- Preview button for images/videos --}}
                    <template x-if="previews[index] && (getFileCategory(file.type) === 'image' || getFileCategory(file.type) === 'video')">
                        <button type="button" @click.stop="openLightbox(previews[index], getFileCategory(file.type), file.name)" class="text-gray-400 dark:text-gray-500 hover:text-lavender-500 transition p-1 min-h-[44px] min-w-[44px] inline-flex items-center justify-center" title="Lihat preview">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </template>

                    <button
                        type="button"
                        class="text-gray-400 dark:text-gray-500 hover:text-red-500 transition p-1 min-h-[44px] min-w-[44px] inline-flex items-center justify-center"
                        @click="removeFile(index)"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </template>
        </div>
    </template>

    {{-- Existing Media Preview --}}
    <template x-if="existingMedia.length > 0">
        <div class="mt-3 space-y-2">
            <p class="text-xs text-gray-400 dark:text-gray-500 font-medium">{{ __('Media yang sudah ada:') }}</p>
            <template x-for="(media, index) in existingMedia" :key="media.id || index">
                <div class="flex items-center gap-3 p-3 bg-mintGreen-50 dark:bg-mintGreen-950/30 border border-mintGreen-200 dark:border-mintGreen-900/30 rounded-xl">
                    <template x-if="media.type === 'photo' && media.url">
                        <button type="button" @click.stop="openLightbox(media.url, 'image', media.name)" class="w-14 h-14 rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-600 shrink-0">
                            <img :src="media.url" :alt="media.name" class="w-full h-full object-cover" />
                        </button>
                    </template>
                    <template x-if="media.type === 'video' && media.url">
                        <div class="w-14 h-14 rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-600 shrink-0 relative">
                            <video :src="media.url" class="w-full h-full object-cover" muted preload="metadata"></video>
                            <div class="absolute inset-0 flex items-center justify-center bg-black/30">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            </div>
                        </div>
                    </template>
                    <template x-if="!media.url || (media.type !== 'photo' && media.type !== 'video')">
                        <span class="text-2xl" x-text="media.type === 'photo' ? '🖼️' : media.type === 'video' ? '🎬' : media.type === 'audio' ? '🎵' : '📄'"></span>
                    </template>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-200 truncate" x-text="media.name"></p>
                        <p class="text-xs text-gray-400 dark:text-gray-500" x-text="media.size"></p>
                    </div>
                </div>
            </template>
        </div>
    </template>

    {{-- Existing media IDs for edit forms --}}
    @if($multiple)
        <template x-for="(media, index) in existingMedia" :key="'existing-'+index">
            <input type="hidden" name="existing_media[]" :value="media.id">
        </template>
    @endif

    {{-- Lightbox --}}
    <div
        x-show="lightboxOpen"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4"
        @click.self="closeLightbox()"
        @keydown.escape.window="closeLightbox()"
        style="display: none;"
    >
        <div class="relative max-w-full max-h-full flex flex-col items-center" @click.stop>
            {{-- Close button --}}
            <button type="button" @click="closeLightbox()" class="absolute -top-12 right-0 sm:top-0 sm:right-0 text-white hover:text-gray-300 transition p-2 min-h-[44px] min-w-[44px] inline-flex items-center justify-center z-10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            {{-- Filename --}}
            <p class="text-white text-sm mb-3 text-center truncate max-w-[80vw]" x-text="lightboxName"></p>

            {{-- Image --}}
            <template x-if="lightboxType === 'image'">
                <img :src="lightboxSrc" :alt="lightboxName" class="max-w-full max-h-[75vh] object-contain rounded-2xl shadow-2xl" />
            </template>

            {{-- Video --}}
            <template x-if="lightboxType === 'video'">
                <video :src="lightboxSrc" controls autoplay class="max-w-full max-h-[75vh] rounded-2xl shadow-2xl"></video>
            </template>
        </div>
    </div>
</div>
