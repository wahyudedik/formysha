<x-app-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('calendar.index', $child) }}" class="text-sm text-skyBlue-600 hover:text-skyBlue-700 dark:text-skyBlue-400 dark:hover:text-skyBlue-300 transition">
                ← Kembali ke Kalender
            </a>
            <h2 class="mt-1 font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                📅 {{ $event->title }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl">
            <!-- Header -->
            <div class="p-4 sm:p-6 lg:p-8 bg-gradient-to-br from-mintGreen-50 to-skyBlue-50 dark:from-mintGreen-950/30 dark:to-skyBlue-950/30 border-b border-mintGreen-100 dark:border-mintGreen-900/30">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $event->title }}</h1>
                        <p class="mt-1 text-gray-600 dark:text-gray-300">{{ $event->event_type_label }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        @if ($event->is_upcoming)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-skyBlue-100 dark:bg-skyBlue-950/30 text-skyBlue-700 dark:text-skyBlue-400">
                                ✅ Mendatang
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                📌 Selesai
                            </span>
                        @endif
                        @if ($event->is_recurring)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-lavender-100 dark:bg-lavender-950/30 text-lavender-700 dark:text-lavender-400">
                                🔁 {{ ucfirst($event->recurrence_pattern ?? 'repeat') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Details -->
            <div class="p-4 sm:p-6 lg:p-8">
                <!-- Date & Time -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <div class="p-4 rounded-2xl bg-mintGreen-50 dark:bg-mintGreen-950/30 border border-mintGreen-100 dark:border-mintGreen-900/30">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">📅 Tanggal</p>
                        <p class="font-semibold text-gray-800 dark:text-gray-100">{{ $event->formatted_date }}</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-skyBlue-50 dark:bg-skyBlue-950/30 border border-skyBlue-100 dark:border-skyBlue-900/30">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">🕐 Waktu</p>
                        <p class="font-semibold text-gray-800 dark:text-gray-100">{{ $event->formatted_time ?? '—' }}</p>
                    </div>
                </div>

                <!-- Description -->
                @if ($event->description)
                    <div class="mb-6">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">📝 Deskripsi</h3>
                        <div class="text-gray-700 dark:text-gray-200 leading-relaxed whitespace-pre-line">{{ $event->description }}</div>
                    </div>
                @endif

                <!-- Reminder -->
                @if ($event->reminder_at)
                    <div class="mb-6 p-4 rounded-2xl bg-warmYellow-50 dark:bg-warmYellow-950/30 border border-warmYellow-100 dark:border-warmYellow-900/30">
                        <p class="text-sm text-gray-600 dark:text-gray-300">🔔 Pengingat: <strong>{{ $event->reminder_at->locale('id')->isoFormat('D MMMM YYYY, HH:mm') }}</strong></p>
                    </div>
                @endif

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 mt-8 pt-6 border-t border-gray-100 dark:border-gray-700">
                    <a href="{{ route('calendar.edit', [$child, $event]) }}" class="btn-primary min-h-[44px]">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        {{ __('Edit') }}
                    </a>
                    <button
                        x-data
                        @click="$dispatch('open-modal', 'confirm-event-deletion')"
                        class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-950/30 rounded-2xl hover:bg-red-100 dark:hover:bg-red-950/50 transition min-h-[44px]"
                    >
                        <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        {{ __('Hapus') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <x-modal name="confirm-event-deletion" :show="false">
        <div class="p-6">
            <div class="relative bg-white dark:bg-gray-800 rounded-3xl shadow-soft max-w-md w-full p-6 z-10">
                <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-2">{{ __('Hapus Acara?') }}</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-6">{{ __('Tindakan ini tidak dapat dibatalkan.') }}</p>
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3">
                    <button
                        x-data
                        @click="$dispatch('close-modal', 'confirm-event-deletion')"
                        class="btn-secondary min-h-[44px]"
                    >
                        Batal
                    </button>
                    <form method="POST" action="{{ route('calendar.destroy', [$child, $event]) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-2xl hover:bg-red-700 transition min-h-[44px]">
                            {{ __('Ya, Hapus') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </x-modal>
</x-app-layout>
