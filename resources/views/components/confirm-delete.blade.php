@props([
    'id' => 'delete-confirm',
    'title' => 'Hapus Data',
    'message' => 'Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.',
    'action' => '#',
    'method' => 'DELETE',
])

<div
    x-data="{ open: false }"
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
        class="relative bg-white dark:bg-gray-800 rounded-3xl shadow-xl max-w-md w-full p-6"
    >
        <div class="text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-red-100 flex items-center justify-center">
                <span class="text-3xl">🗑️</span>
            </div>
            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-2">{{ $title }}</h3>
            <p class="text-gray-500 dark:text-gray-400 text-sm mb-6">{{ $message }}</p>
            <div class="flex gap-3 justify-center">
                <button
                    x-on:click="open = false"
                    class="px-5 py-2.5 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                >
                    Batal
                </button>
                <form method="POST" action="{{ $action }}" class="inline">
                    @csrf
                    @method($method)
                    <button
                        type="submit"
                        class="px-5 py-2.5 rounded-xl bg-red-500 text-white text-sm font-medium hover:bg-red-600 transition-colors"
                    >
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
