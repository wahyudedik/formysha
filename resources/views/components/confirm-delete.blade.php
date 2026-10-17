@props([
    'id' => 'delete-confirm',
    'title' => 'Hapus Data',
    'message' => 'Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.',
    'action' => '#',
    'method' => 'DELETE',
])

<div
    x-data="{ open: false, loading: false }"
    x-on:delete-confirm.window="if($event.detail === '{{ $id }}') open = true"
    x-on:keydown.escape.window="open = false"
    x-show="open"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    style="display: none;"
>
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-gray-500/70 dark:bg-gray-900/70" x-on:click="open = false"></div>

    {{-- Modal --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="relative bg-white dark:bg-gray-800 rounded-3xl shadow-xl max-w-md w-full p-4 sm:p-6 max-h-[90vh] overflow-y-auto"
    >
        <div class="text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-red-100 dark:bg-red-950/30 flex items-center justify-center">
                <span class="text-3xl">🗑️</span>
            </div>
            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-2">{{ $title }}</h3>
            <p class="text-gray-500 dark:text-gray-400 text-sm mb-6">{{ $message }}</p>
            <form method="POST" action="{{ $action }}" class="text-left" x-on:submit="loading = true">
                @csrf
                @method($method)

                @if (trim((string) $slot) !== '')
                    <div class="mb-4">{{ $slot }}</div>
                @endif

                <div class="flex gap-3 justify-center">
                    <button
                        type="button"
                        x-on:click="open = false"
                        class="px-5 py-2.5 min-h-[44px] rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                    >
                        {{ __('common.cancel') }}
                    </button>
                    <button
                        type="submit"
                        :disabled="loading"
                        class="px-5 py-2.5 min-h-[44px] rounded-xl bg-red-500 text-white text-sm font-medium hover:bg-red-600 transition-colors disabled:opacity-60"
                    >
                        <span x-show="!loading">{{ __('common.confirm_delete') }}</span>
                        <span x-show="loading" x-cloak class="inline-flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            {{ __('common.loading') }}
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
