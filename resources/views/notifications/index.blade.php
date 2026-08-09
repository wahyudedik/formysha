<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100 leading-tight">
                🔔 Notifikasi
            </h2>
            @if($unreadCount > 0)
                <form method="POST" action="{{ route('notifications.markAllRead') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-skyBlue/10 text-skyBlue-700 rounded-xl text-sm font-medium hover:bg-skyBlue/20 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Tandai Semua Sudah Dibaca
                    </button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Summary -->
        <div class="mb-6 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-4">
            <div class="flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                <span class="inline-flex items-center gap-1">
                    <span class="w-2 h-2 bg-skyBlue rounded-full"></span>
                    Total: {{ $notifications->total() }} notifikasi
                </span>
                @if($unreadCount > 0)
                    <span class="inline-flex items-center gap-1">
                        <span class="w-2 h-2 bg-softPink rounded-full"></span>
                        Belum dibaca: {{ $unreadCount }}
                    </span>
                @endif
            </div>
        </div>

        @if($notifications->isEmpty())
            <div class="text-center py-12">
                <div class="text-6xl mb-4">🔔</div>
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-2">Belum Ada Notifikasi</h3>
                <p class="text-gray-500 dark:text-gray-400">Notifikasi baru akan muncul di sini ketika ada aktivitas penting.</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($notifications as $notification)
                    @include('notifications.partials.notification-item', ['notification' => $notification])
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
