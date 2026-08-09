<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('documents.index', $child) }}" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                    📄 {{ $document->name }}
                </h2>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('documents.edit', [$child, $document]) }}" class="btn-secondary text-sm min-h-[44px]">
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

            <!-- Document Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl">
                <!-- Header -->
                <div class="bg-gradient-to-r from-skyBlue-50 to-lavender-50 dark:from-skyBlue-950/30 dark:via-gray-800 dark:to-lavender-950/30 p-6 sm:p-8">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start gap-4">
                            <div class="text-4xl">{{ $document->type_label }}</div>
                            <div>
                                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $document->name }}</h1>
                                <p class="mt-1 text-gray-600 dark:text-gray-300">{{ $document->type_label }}</p>
                                <div class="mt-2 flex flex-wrap items-center gap-3 text-sm text-gray-500 dark:text-gray-400">
                                    <span>📎 {{ $document->formatted_size }}</span>
                                    @if ($document->is_private)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs">🔒 {{ __('Privat') }}</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-mintGreen-100 dark:bg-mintGreen-950/30 text-mintGreen-600 dark:text-mintGreen-400 text-xs">🌐 {{ __('Publik') }}</span>
                                    @endif
                                    @if ($document->is_expired)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-red-100 dark:bg-red-950/30 text-red-600 dark:text-red-400 text-xs">⚠️ {{ __('Kedaluwarsa') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Delete Button -->
                        <button
                            x-data
                            x-on:click.prevent="$dispatch('delete-confirm', 'delete-document-{{ $document->id }}')"
                            class="text-gray-400 hover:text-red-500 dark:text-gray-500 dark:hover:text-red-400 transition p-2 rounded-xl hover:bg-red-50 dark:hover:bg-red-950/30 min-h-[44px] min-w-[44px] inline-flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                        <x-confirm-delete
                            id="delete-document-{{ $document->id }}"
                            title="{{ __('Hapus Dokumen?') }}"
                            message="{{ __('Tindakan ini tidak dapat dibatalkan.') }}"
                            action="{{ route('documents.destroy', [$child, $document]) }}"
                        />
                    </div>
                </div>

                <!-- Details -->
                <div class="p-6 sm:p-8 space-y-4">
                    @if ($document->description)
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('Deskripsi') }}</h3>
                            <p class="text-gray-700 dark:text-gray-200">{{ $document->description }}</p>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @if ($document->issued_date)
                            <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-2xl">
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Tanggal Terbit') }}</p>
                                <p class="font-medium text-gray-700 dark:text-gray-200">{{ $document->formatted_issued_date }}</p>
                            </div>
                        @endif

                        @if ($document->expiry_date)
                            <div class="p-4 {{ $document->is_expired ? 'bg-red-50 dark:bg-red-950/30' : 'bg-gray-50 dark:bg-gray-700/50' }} rounded-2xl">
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Tanggal Kedaluwarsa') }}</p>
                                <p class="font-medium {{ $document->is_expired ? 'text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-gray-200' }}">{{ $document->formatted_expiry_date }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- File Info -->
                    <div class="p-4 bg-gradient-to-r from-skyBlue-50 to-lavender-50 dark:from-skyBlue-950/30 dark:to-lavender-950/30 rounded-2xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ $document->file_name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $document->formatted_size }}</p>
                            </div>
                            <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank" class="btn-primary text-sm min-h-[44px]">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                {{ __('Download') }}
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-4 sm:px-6 lg:px-8 py-4 border-t border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 text-xs text-gray-400 dark:text-gray-500">
                    <span>{{ __('Diunggah oleh') }} {{ $document->user->name }}</span>
                    <span>{{ $document->created_at->locale('id')->isoFormat('D MMMM YYYY, HH:mm') }}</span>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
