@props(['position' => 'top-right'])

@if(session('success') || session('error') || session('warning') || session('info') || session('status'))
    <div
        x-data="{
            show: true,
            type: '{{ session('success') ? 'success' : (session('error') ? 'error' : (session('warning') ? 'warning' : 'info')) }}',
            message: '{{ session('success') ?? session('error') ?? session('warning') ?? session('info') ?? session('status') }}',
            icons: {
                success: '✅',
                error: '❌',
                warning: '⚠️',
                info: 'ℹ️'
            },
            colors: {
                success: 'bg-mintGreen-50 border-mintGreen-200 text-mintGreen-800 dark:bg-mintGreen-950/30 dark:border-mintGreen-800 dark:text-mintGreen-300',
                error: 'bg-red-50 border-red-200 text-red-800 dark:bg-red-950/30 dark:border-red-800 dark:text-red-300',
                warning: 'bg-warmYellow-50 border-warmYellow-200 text-warmYellow-800 dark:bg-warmYellow-950/30 dark:border-warmYellow-800 dark:text-warmYellow-300',
                info: 'bg-skyBlue-50 border-skyBlue-200 text-skyBlue-800 dark:bg-skyBlue-950/30 dark:border-skyBlue-800 dark:text-skyBlue-300'
            }
        }"
        x-init="setTimeout(() => { show = false }, 5000)"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform -translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform -translate-y-2"
        class="fixed top-4 left-1/2 -translate-x-1/2 sm:translate-x-0 sm:right-4 sm:left-auto z-50 max-w-sm w-[calc(100%-2rem)] sm:w-full"
    >
        <div class="flex items-center gap-3 p-4 rounded-2xl border shadow-lg" :class="colors[type]">
            <span class="text-lg" x-text="icons[type]"></span>
            <p class="text-sm font-medium flex-1" x-text="message"></p>
            <button @click="show = false" class="text-current opacity-50 hover:opacity-100 transition-opacity min-h-[44px] min-w-[44px] inline-flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
@endif
