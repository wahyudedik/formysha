<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('children.show', $child) }}" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                    {{ __('Koneksi') }} — {{ $child->nickname ?? $child->name }}
                </h2>
            </div>
            <a href="{{ route('connections.create', $child) }}" class="btn-primary text-sm min-h-[44px]">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('Tambah Koneksi') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 p-4 bg-mintGreen-50 border border-mintGreen-200 text-mintGreen-700 dark:bg-mintGreen-950/30 dark:border-mintGreen-800 dark:text-mintGreen-400 rounded-xl" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)">
                    {{ session('status') }}
                </div>
            @endif

            @if ($connections->isEmpty())
                <!-- Empty State -->
                <div class="text-center py-16">
                    <div class="text-6xl mb-4">🔗</div>
                    <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-2">{{ __('Belum Ada Koneksi') }}</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-6">{{ __('Hubungkan ') . ($child->nickname ?? $child->name) . __(' dengan fasilitas kesehatan atau sekolah.') }}</p>
                    <a href="{{ route('connections.create', $child) }}" class="btn-primary min-h-[44px]">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        {{ __('Tambah Koneksi') }}
                    </a>
                </div>
            @else
                <!-- Connections List -->
                <div class="space-y-4">
                    @foreach ($connections as $connection)
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-2xl p-4 sm:p-6 flex flex-col sm:flex-row items-start sm:items-center gap-4">
                            <!-- Icon -->
                            <div class="shrink-0">
                                <div class="w-14 h-14 rounded-2xl bg-skyBlue-100 dark:bg-skyBlue-950/30 flex items-center justify-center text-2xl shadow-soft">
                                    🏥
                                </div>
                            </div>

                            <!-- Info -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="font-bold text-gray-800 dark:text-gray-100 truncate">{{ $connection->tenant->name ?? '-' }}</h3>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-medium
                                        {{ match($connection->status->value) {
                                            'active' => 'bg-mintGreen-100 dark:bg-mintGreen-950/30 text-mintGreen-600 dark:text-mintGreen-400',
                                            'pending' => 'bg-warmYellow-100 dark:bg-warmYellow-950/30 text-warmYellow-600 dark:text-warmYellow-400',
                                            'referred' => 'bg-skyBlue-100 dark:bg-skyBlue-950/30 text-skyBlue-600 dark:text-skyBlue-400',
                                            default => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400',
                                        } }}">
                                        {{ $connection->status->label() }}
                                    </span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-medium
                                        {{ match($connection->permission->value) {
                                            'manage' => 'bg-softPink-100 dark:bg-softPink-950/30 text-softPink-600 dark:text-softPink-400',
                                            'edit' => 'bg-skyBlue-100 dark:bg-skyBlue-950/30 text-skyBlue-600 dark:text-skyBlue-400',
                                            'comment' => 'bg-lavender-100 dark:bg-lavender-950/30 text-lavender-600 dark:text-lavender-400',
                                            default => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400',
                                        } }}">
                                        {{ $connection->permission->label() }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $connection->permission->description() }}</p>
                                @if ($connection->invited_at)
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">📅 {{ __('Dibuat:') }} {{ $connection->invited_at->diffForHumans() }}</p>
                                @endif
                                @if ($connection->accepted_at)
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">✅ {{ __('Disetujui:') }} {{ $connection->accepted_at->diffForHumans() }}</p>
                                @endif
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center gap-2 shrink-0">
                                <a href="{{ route('connections.show', [$child, $connection]) }}" class="p-2 text-gray-400 hover:text-skyBlue-500 hover:bg-skyBlue-50 dark:text-gray-500 dark:hover:text-skyBlue-400 dark:hover:bg-skyBlue-950/20 rounded-xl transition min-h-[44px] min-w-[44px] inline-flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                @if ($connection->isPending())
                                    <form method="POST" action="{{ route('connections.approve', [$child, $connection]) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="p-2 text-gray-400 hover:text-mintGreen-500 hover:bg-mintGreen-50 dark:text-gray-500 dark:hover:text-mintGreen-400 dark:hover:bg-mintGreen-950/20 rounded-xl transition min-h-[44px] min-w-[44px] inline-flex items-center justify-center" title="{{ __('Setujui') }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                                <button type="button" class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 dark:text-gray-500 dark:hover:text-red-400 dark:hover:bg-red-950/30 rounded-xl transition min-h-[44px] min-w-[44px] inline-flex items-center justify-center"
                                    x-data
                                    x-on:click.prevent="$dispatch('delete-confirm', 'delete-connection-{{ $connection->id }}')">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Delete Confirmation Modals --}}
                @foreach ($connections as $connection)
                    <x-confirm-delete
                        id="delete-connection-{{ $connection->id }}"
                        title="{{ __('Hapus Koneksi') }}"
                        message="{{ __('Apakah Anda yakin ingin menghapus koneksi dengan ' . ($connection->tenant->name ?? '-') . '? Tindakan ini tidak dapat dibatalkan.') }}"
                        action="{{ route('connections.destroy', [$child, $connection]) }}"
                    />
                @endforeach
            @endif
        </div>
    </div>
</x-app-layout>
