<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('connections.index', $child) }}" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                    {{ __('Detail Koneksi') }}
                </h2>
            </div>
            <div class="flex items-center gap-2">
                @if ($connection->isPending())
                    <form method="POST" action="{{ route('connections.approve', [$child, $connection]) }}" class="inline">
                        @csrf
                        <button type="submit" class="btn-primary text-sm min-h-[44px]">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            {{ __('Setujui') }}
                        </button>
                    </form>
                    <form method="POST" action="{{ route('connections.reject', [$child, $connection]) }}" class="inline" x-data="{ loading: false }" @submit="loading = true">
                        @csrf
                        <button type="submit" :disabled="loading" class="btn-secondary text-sm min-h-[44px] border-red-300 text-red-600 hover:bg-red-50 dark:border-red-700 dark:text-red-400 dark:hover:bg-red-950/20">
                            {{ __('Tolak') }}
                        </button>
                    </form>
                @endif
                @if ($connection->isActive())
                    <a href="{{ route('connections.edit', [$child, $connection]) }}" class="btn-secondary text-sm min-h-[44px]">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        {{ __('Edit') }}
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 p-4 bg-mintGreen-50 border border-mintGreen-200 text-mintGreen-700 dark:bg-mintGreen-950/30 dark:border-mintGreen-800 dark:text-mintGreen-400 rounded-xl" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Connection Info -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-2xl p-4 sm:p-6 mb-6">
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 mb-6">
                    <div class="w-16 h-16 rounded-2xl bg-skyBlue-100 dark:bg-skyBlue-950/30 flex items-center justify-center text-3xl shadow-soft">
                        🏥
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ $connection->tenant->name ?? '-' }}</h3>
                        <div class="flex items-center gap-2 mt-1 flex-wrap">
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
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                        <p class="text-xs text-gray-400 dark:text-gray-500 mb-1">{{ __('Level Akses') }}</p>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $connection->permission->description() }}</p>
                    </div>
                    <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                        <p class="text-xs text-gray-400 dark:text-gray-500 mb-1">{{ __('common.status') }}</p>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $connection->status->label() }}</p>
                    </div>
                    @if ($connection->invitedBy)
                        <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                            <p class="text-xs text-gray-400 dark:text-gray-500 mb-1">{{ __('Diundang Oleh') }}</p>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $connection->invitedBy->name }}</p>
                        </div>
                    @endif
                    @if ($connection->invited_at)
                        <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                            <p class="text-xs text-gray-400 dark:text-gray-500 mb-1">{{ __('Tanggal Dibuat') }}</p>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $connection->invited_at->format('d M Y H:i') }}</p>
                        </div>
                    @endif
                    @if ($connection->accepted_at)
                        <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                            <p class="text-xs text-gray-400 dark:text-gray-500 mb-1">{{ __('Tanggal Disetujui') }}</p>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $connection->accepted_at->format('d M Y H:i') }}</p>
                        </div>
                    @endif
                    @if ($connection->expires_at)
                        <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                            <p class="text-xs text-gray-400 dark:text-gray-500 mb-1">{{ __('Berlaku Hingga') }}</p>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $connection->expires_at->format('d M Y H:i') }}</p>
                        </div>
                    @endif
                    @if ($connection->notes)
                        <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl sm:col-span-2">
                            <p class="text-xs text-gray-400 dark:text-gray-500 mb-1">{{ __('Catatan') }}</p>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $connection->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Activity History -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-2xl p-4 sm:p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Riwayat Aktivitas') }}</h3>

                @if ($activities->isEmpty())
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('empty_states.no_activity') }}</p>
                @else
                    <div class="space-y-3">
                        @foreach ($activities as $activity)
                            <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                                <div class="shrink-0 w-8 h-8 rounded-full bg-lavender-100 dark:bg-lavender-950/30 flex items-center justify-center text-sm">
                                    {{ match(substr($activity->action, 0, 20)) {
                                        'connection.created' => '🔗',
                                        'connection.approved' => '✅',
                                        'connection.rejected' => '❌',
                                        'connection.revoked' => '🚫',
                                        'connection.permission' => '🔑',
                                        'connection.expired' => '⏰',
                                        default => '📝',
                                    } }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $activity->description ?? $activity->action }}</p>
                                    <div class="flex items-center gap-2 mt-1">
                                        @if ($activity->user)
                                            <span class="text-xs text-gray-400 dark:text-gray-500">{{ $activity->user->name }}</span>
                                        @endif
                                        <span class="text-xs text-gray-400 dark:text-gray-500">·</span>
                                        <span class="text-xs text-gray-400 dark:text-gray-500">{{ $activity->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Danger Zone -->
            @if ($connection->isActive() || $connection->isPending())
                <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-2xl p-4 sm:p-6 border border-red-200 dark:border-red-800">
                    <h3 class="text-lg font-semibold text-red-600 dark:text-red-400 mb-2">{{ __('Zona Bahaya') }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ __('Mencabut koneksi akan menghapus akses organisasi ini ke data anak Anda.') }}</p>
                    <button type="button" class="btn-secondary min-h-[44px] border-red-300 text-red-600 hover:bg-red-50 dark:border-red-700 dark:text-red-400 dark:hover:bg-red-950/20"
                        x-data
                        x-on:click.prevent="$dispatch('delete-confirm', 'revoke-connection-{{ $connection->id }}')">
                        {{ __('Cabut Koneksi') }}
                    </button>
                </div>
            @endif
        </div>
    </div>

    <x-confirm-delete
        id="revoke-connection-{{ $connection->id }}"
        title="{{ __('Cabut Koneksi') }}"
        message="{{ __('Apakah Anda yakin ingin mencabut koneksi dengan ' . ($connection->tenant->name ?? '-') . '? Organisasi ini tidak akan bisa mengakses data anak lagi.') }}"
        action="{{ route('connections.revoke', [$child, $connection]) }}"
    />
</x-app-layout>
