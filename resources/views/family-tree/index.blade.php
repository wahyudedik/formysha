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
                    {{ __('Pohon Keluarga') }} — {{ $child->nickname ?? $child->name }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Section 1: Owner Info -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-2xl p-4 sm:p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">👤 {{ __('Pemilik Data') }}</h3>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-softPink-100 dark:bg-softPink-950/30 flex items-center justify-center text-2xl shadow-soft">
                        👨‍👩‍👧
                    </div>
                    <div>
                        <p class="font-bold text-gray-800 dark:text-gray-100">{{ $tree['owner']['name'] }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $tree['owner']['email'] }}</p>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-medium bg-softPink-100 dark:bg-softPink-950/30 text-softPink-600 dark:text-softPink-400 mt-1">
                            {{ $tree['owner']['role'] }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Section 2: Family Members -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-2xl p-4 sm:p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">👨‍👩‍👧‍👦 {{ __('Anggota Keluarga') }}</h3>
                @if ($familyMembers->isEmpty())
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('empty_states.no_family_members') }}</p>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach ($familyMembers as $member)
                            <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl flex items-center gap-3">
                                @if ($member->photo)
                                    <img src="{{ asset('storage/' . $member->photo) }}" alt="{{ $member->name }}" class="w-10 h-10 rounded-xl object-cover" />
                                @else
                                    <div class="w-10 h-10 rounded-xl bg-lavender-100 dark:bg-lavender-950/30 flex items-center justify-center text-lg">
                                        {{ match($member->relationship) {
                                            'father' => '👨',
                                            'mother' => '👩',
                                            'grandfather' => '👴',
                                            'grandmother' => '👵',
                                            'sibling' => '🧒',
                                            default => '👤',
                                        } }}
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <p class="font-medium text-gray-800 dark:text-gray-100 text-sm truncate">{{ $member->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $member->relationship_label }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Section 3: Connected Organizations -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-2xl p-4 sm:p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">🏥 {{ __('Organisasi Terhubung') }}</h3>
                @if ($connections->isEmpty())
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('empty_states.no_organizations_connected') }}</p>
                @else
                    <div class="space-y-3">
                        @foreach ($connections as $connection)
                            <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl flex flex-col sm:flex-row sm:items-center gap-2">
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-gray-800 dark:text-gray-100 text-sm">{{ $connection->tenant->name ?? '-' }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $connection->tenant->type ?? '-' }}</p>
                                </div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-medium
                                        {{ match($connection->status->value) {
                                            'active' => 'bg-mintGreen-100 dark:bg-mintGreen-950/30 text-mintGreen-600 dark:text-mintGreen-400',
                                            'pending' => 'bg-warmYellow-100 dark:bg-warmYellow-950/30 text-warmYellow-600 dark:text-warmYellow-400',
                                            default => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400',
                                        } }}">
                                        {{ $connection->status->label() }}
                                    </span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-medium
                                        {{ match($connection->permission->value) {
                                            'manage' => 'bg-softPink-100 dark:bg-softPink-950/30 text-softPink-600 dark:text-softPink-400',
                                            'edit' => 'bg-skyBlue-100 dark:bg-skyBlue-950/30 text-skyBlue-600 dark:text-skyBlue-400',
                                            default => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400',
                                        } }}">
                                        {{ $connection->permission->label() }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Section 4: Recent Activity -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-2xl p-4 sm:p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">📋 {{ __('Aktivitas Terbaru') }}</h3>
                @if ($recentActivity->isEmpty())
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('empty_states.no_activity') }}</p>
                @else
                    <div class="space-y-3">
                        @foreach ($recentActivity as $activity)
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
        </div>
    </div>
</x-app-layout>
