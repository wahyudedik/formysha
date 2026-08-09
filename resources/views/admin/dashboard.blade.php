<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            🏢 {{ __('Dashboard Admin') }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-6">
            {{-- Sidebar --}}
            @include('admin.partials.sidebar')

            {{-- Main Content --}}
            <div class="flex-1 min-w-0">
                {{-- Welcome Message --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-6 mb-6 border border-gray-100 dark:border-gray-700">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-skyBlue-400 to-lavender-400 flex items-center justify-center text-white text-2xl shrink-0">
                            @if ($tenant->logo)
                                <img src="{{ Storage::disk('public')->url($tenant->logo) }}" alt="{{ $tenant->name }}" class="w-14 h-14 rounded-2xl object-cover">
                            @else
                                {{ strtoupper(substr($tenant->name, 0, 1)) }}
                            @endif
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">Selamat Datang, {{ $user->name ?? Auth::user()->name }}! 👋</h1>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Mengelola <strong>{{ $tenant->name }}</strong></p>
                            @if ($plan)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium bg-skyBlue-100 dark:bg-skyBlue-950/30 text-skyBlue-700 dark:text-skyBlue-400 mt-1">
                                    Paket: {{ $plan->name }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Stats Cards --}}
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    {{-- Total Anak --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-5 border border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-softPink-50 dark:bg-softPink-950/30 flex items-center justify-center">
                                <span class="text-2xl">👶</span>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $totalChildren }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Total Anak</p>
                            </div>
                        </div>
                    </div>

                    {{-- Total Timeline --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-5 border border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-skyBlue-50 dark:bg-skyBlue-950/30 flex items-center justify-center">
                                <span class="text-2xl">📅</span>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $totalTimelines }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Timeline</p>
                            </div>
                        </div>
                    </div>

                    {{-- Total Foto --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-5 border border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-mintGreen-50 dark:bg-mintGreen-950/30 flex items-center justify-center">
                                <span class="text-2xl">📷</span>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $totalPhotos }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Foto</p>
                            </div>
                        </div>
                    </div>

                    {{-- Total Dokumen --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-5 border border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-lavender-50 dark:bg-lavender-950/30 flex items-center justify-center">
                                <span class="text-2xl">📄</span>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $totalDocuments }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Dokumen</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Recent Activity --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft overflow-hidden">
                        <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="font-semibold text-gray-800 dark:text-gray-100">📋 {{ __('Aktivitas Terbaru') }}</h3>
                        </div>
                        <div class="p-6">
                            @if ($recentActivity->isEmpty())
                                <div class="text-center py-8">
                                    <div class="text-4xl mb-3">📝</div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada aktivitas.</p>
                                </div>
                            @else
                                <div class="space-y-3">
                                    @foreach ($recentActivity as $activity)
                                        <div class="flex items-start gap-3 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                            <div class="w-8 h-8 rounded-lg bg-skyBlue-50 dark:bg-skyBlue-950/30 flex items-center justify-center text-sm shrink-0">
                                                @if ($activity instanceof \App\Models\Timeline)
                                                    📅
                                                @elseif ($activity instanceof \App\Models\Diary)
                                                    📝
                                                @else
                                                    📌
                                                @endif
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-sm font-medium text-gray-800 dark:text-gray-100 truncate">{{ $activity->title ?? 'Tanpa judul' }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $activity->child->name ?? '-' }} · {{ $activity->created_at->locale('id')->diffForHumans() }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Quick Actions --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-6">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-100 mb-4">🚀 {{ __('Aksi Cepat') }}</h3>
                        <div class="grid grid-cols-2 gap-3">
                            @if ($totalChildren > 0)
                                <a href="{{ route('children.index') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl bg-softPink-50 dark:bg-softPink-950/30 hover:bg-softPink-100 dark:hover:bg-softPink-950/50 transition text-center">
                                    <span class="text-2xl">👶</span>
                                    <span class="text-xs font-medium text-softPink-700 dark:text-softPink-400">Lihat Anak</span>
                                </a>
                            @endif
                            <a href="{{ route('admin.usage.index') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl bg-skyBlue-50 dark:bg-skyBlue-950/30 hover:bg-skyBlue-100 dark:hover:bg-skyBlue-950/50 transition text-center">
                                <span class="text-2xl">📊</span>
                                <span class="text-xs font-medium text-skyBlue-700 dark:text-skyBlue-400">Penggunaan</span>
                            </a>
                            <a href="{{ route('admin.branding.edit') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl bg-lavender-50 dark:bg-lavender-950/30 hover:bg-lavender-100 dark:hover:bg-lavender-950/50 transition text-center">
                                <span class="text-2xl">🎨</span>
                                <span class="text-xs font-medium text-lavender-700 dark:text-lavender-400">Branding</span>
                            </a>
                            <a href="{{ route('admin.settings.edit') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl bg-mintGreen-50 dark:bg-mintGreen-950/30 hover:bg-mintGreen-100 dark:hover:bg-mintGreen-950/50 transition text-center">
                                <span class="text-2xl">⚙️</span>
                                <span class="text-xs font-medium text-mintGreen-700 dark:text-mintGreen-400">Pengaturan</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
