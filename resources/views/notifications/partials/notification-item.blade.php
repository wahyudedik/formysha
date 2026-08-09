@props(['notification'])

<div class="group relative bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 transition-all hover:shadow-md {{ $notification->is_read ? 'opacity-75' : 'border-l-4 border-l-skyBlue' }}">
    <div class="flex items-start gap-4">
        <!-- Icon -->
        <div class="flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center text-lg {{ $notification->type_color }}">
            {{ $notification->icon ?? '📋' }}
        </div>

        <!-- Content -->
        <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between gap-2">
                <div class="flex-1 min-w-0">
                    <h4 class="font-semibold text-gray-800 dark:text-gray-100 text-sm {{ $notification->is_read ? 'font-normal' : '' }}">
                        {{ $notification->title }}
                    </h4>
                    <p class="text-gray-600 dark:text-gray-300 text-sm mt-1 line-clamp-2">{{ $notification->message }}</p>
                </div>
                @if(! $notification->is_read)
                    <span class="flex-shrink-0 w-2.5 h-2.5 bg-skyBlue rounded-full mt-1.5"></span>
                @endif
            </div>

            <div class="flex items-center gap-3 mt-2 text-xs text-gray-400 dark:text-gray-500">
                <span>{{ $notification->formatted_date }}</span>
                @if($notification->child)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-gray-100 dark:bg-gray-700 rounded-full">
                        👶 {{ $notification->child->name }}
                    </span>
                @endif
                <span class="px-2 py-0.5 rounded-full {{ $notification->type_color }}">
                    {{ $notification->type_label }}
                </span>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex-shrink-0 flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
            @if(! $notification->is_read)
                <form method="POST" action="{{ route('notifications.markAsRead', $notification) }}">
                    @csrf
                    <button type="submit" class="p-1.5 min-h-[44px] min-w-[44px] inline-flex items-center justify-center text-gray-400 hover:text-skyBlue hover:bg-skyBlue/10 dark:text-gray-500 dark:hover:text-skyBlue-400 dark:hover:bg-skyBlue/10 rounded-lg transition-colors" title="Tandai sudah dibaca">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </button>
                </form>
            @endif
            <button type="submit" class="p-1.5 min-h-[44px] min-w-[44px] inline-flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 dark:text-gray-500 dark:hover:text-red-400 dark:hover:bg-red-950/30 rounded-lg transition-colors" title="Hapus notifikasi"
                x-data
                x-on:click.prevent="$dispatch('delete-confirm', 'delete-notification-{{ $notification->id }}')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        </div>
    </div>
</div>
