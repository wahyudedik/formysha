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
        isDragging: false,
        maxFiles: {{ $maxFiles }},
        maxSizeBytes: 10485760,
        existingMedia: {{ Js::from($existingMedia) }},
        removeFile(index) {
            this.files.splice(index, 1);
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
        getFileIcon(type) {
            if (type.startsWith('image/')) return '🖼️';
            if (type.startsWith('video/')) return '🎬';
            if (type.startsWith('audio/')) return '🎵';
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
            this.addFiles(selectedFiles);
            e.target.value = '';
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
            }
        }
    }"
    class="mb-5"
>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
        {{ __('Media (foto, video, audio)') }}
        <span class="text-gray-400 font-normal">— opsional, maks {{ $maxSize }}</span>
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
        x-ref="fileInput"
        class="sr-only"
        {{ $multiple ? 'multiple' : '' }}
        accept="{{ $accept }}"
        @change="handleFileSelect($event)"
    >

    {{-- New Files Preview --}}
    <template x-if="files.length > 0">
        <div class="mt-3 space-y-2">
            <template x-for="(file, index) in files" :key="index">
                <div class="flex items-center gap-3 p-3 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl">
                    <span class="text-2xl" x-text="getFileIcon(file.type)"></span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-200 truncate" x-text="file.name"></p>
                        <p class="text-xs text-gray-400 dark:text-gray-500" x-text="formatSize(file.size)"></p>
                    </div>
                    <button
                        type="button"
                        class="text-gray-400 dark:text-gray-500 hover:text-red-500 transition p-1"
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
                    <span class="text-2xl" x-text="media.type === 'photo' ? '🖼️' : media.type === 'video' ? '🎬' : media.type === 'audio' ? '🎵' : '📄'"></span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-200 truncate" x-text="media.name"></p>
                        <p class="text-xs text-gray-400 dark:text-gray-500" x-text="media.size"></p>
                    </div>
                </div>
            </template>
        </div>
    </template>

    {{-- Hidden inputs for file count --}}
    @if($multiple)
        <input type="hidden" name="media_count" :value="files.length">
    @endif
</div>
