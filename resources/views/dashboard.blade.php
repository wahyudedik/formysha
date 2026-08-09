<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            🏠 {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Welcome Section -->
        <div class="mb-8 bg-gradient-to-br from-softPink-50 to-lavender-50 dark:from-softPink-950/30 dark:to-lavender-950/30 rounded-3xl p-6 sm:p-8 border border-softPink-100 dark:border-softPink-900/30">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Selamat datang kembali, ') }}{{ auth()->user()->name }} 💕</h1>
            <p class="mt-2 text-gray-500 dark:text-gray-400">{{ __('Bersama setiap langkahmu') }}</p>
        </div>

        <!-- Children Cards -->
        @if ($children->isEmpty())
            <div class="mb-8 bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl">
                <div class="p-8 text-center">
                    <div class="text-5xl mb-4">👶</div>
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-2">{{ __('Belum Ada Anak') }}</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-6">{{ __('Mulai dokumentasikan perjalanan hidup buah hati Anda.') }}</p>
                    <a href="{{ route('children.create') }}" class="btn-primary">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        {{ __('Tambah Anak') }}
                    </a>
                </div>
            </div>
        @else
            <!-- Child Profile & Stats Section -->
            @foreach ($children as $child)
                <div class="mb-8 bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl">
                    <div class="bg-gradient-to-br from-softPink-50 via-cream-50 to-lavender-50 dark:from-softPink-950/30 dark:via-gray-800 dark:to-lavender-950/30 p-6 sm:p-8">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6">
                            <!-- Child Photo -->
                            <a href="{{ route('children.show', $child) }}" class="flex-shrink-0">
                                @if ($child->photo)
                                    <img src="{{ asset('storage/' . $child->photo) }}" alt="{{ $child->name }}" class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl object-cover shadow-soft-md" />
                                @else
                                    <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl {{ $child->gender === 'female' ? 'bg-softPink-100 dark:bg-softPink-950/30' : 'bg-skyBlue-100 dark:bg-skyBlue-950/30' }} flex items-center justify-center text-4xl shadow-soft-md">
                                        {{ $child->gender === 'female' ? '👧' : '👦' }}
                                    </div>
                                @endif
                            </a>

                            <!-- Child Info -->
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('children.show', $child) }}" class="block">
                                    <h3 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $child->nickname ?? $child->name }} 💕</h3>
                                </a>
                                <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">{{ $child->age ?? '—' }}</p>
                                @if ($child->date_of_birth)
                                    <p class="text-gray-400 dark:text-gray-500 text-xs mt-1">🎂 {{ $child->date_of_birth->locale('id')->isoFormat('D MMMM YYYY') }}</p>
                                @endif
                            </div>

                            <!-- Stats Cards -->
                            <div class="flex gap-3 sm:gap-4">
                                <!-- Usia -->
                                <div class="bg-white/80 dark:bg-gray-700/80 backdrop-blur-sm rounded-2xl px-4 py-3 text-center shadow-soft min-w-[80px]">
                                    <div class="text-lg font-bold text-softPink-600 dark:text-softPink-400">🎂</div>
                                    <div class="text-sm font-bold text-gray-800 dark:text-gray-100">{{ $child->age ?? '—' }}</div>
                                    <div class="text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-wide">Usia</div>
                                </div>

                                <!-- Momen -->
                                <div class="bg-white/80 dark:bg-gray-700/80 backdrop-blur-sm rounded-2xl px-4 py-3 text-center shadow-soft min-w-[80px]">
                                    <div class="text-lg font-bold text-mintGreen-600 dark:text-mintGreen-400">📸</div>
                                    <div class="text-sm font-bold text-gray-800 dark:text-gray-100">{{ $totalMediaCount }}</div>
                                    <div class="text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-wide">Momen</div>
                                </div>

                                <!-- Dokumen -->
                                <div class="bg-white/80 dark:bg-gray-700/80 backdrop-blur-sm rounded-2xl px-4 py-3 text-center shadow-soft min-w-[80px]">
                                    <div class="text-lg font-bold text-skyBlue-600 dark:text-skyBlue-400">📄</div>
                                    <div class="text-sm font-bold text-gray-800 dark:text-gray-100">{{ $totalDocumentCount }}</div>
                                    <div class="text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-wide">Dokumen</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Momen Terbaru (Photo Thumbnails) -->
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-100">📸 {{ __('Momen Terbaru') }}</h3>
                        @if ($children->isNotEmpty())
                            <a href="{{ route('timeline.index', $children->first()) }}" class="text-sm text-softPink-500 hover:text-softPink-600 dark:text-softPink-400 dark:hover:text-softPink-300 transition font-medium">
                                {{ __('Lihat Semua') }}
                            </a>
                        @endif
                    </div>
                    @if ($recentMedia->isEmpty())
                        <div class="text-center py-8">
                            <div class="text-4xl mb-3">📷</div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Belum ada momen.') }}</p>
                            @if ($children->isNotEmpty())
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ __('Mulai dokumentasikan momen pertama buah hati.') }}</p>
                            @endif
                        </div>
                    @else
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            @foreach ($recentMedia as $media)
                                <div class="aspect-square rounded-2xl overflow-hidden bg-softPink-50 dark:bg-softPink-950/20 border border-softPink-100 dark:border-softPink-900/30">
                                    <img src="{{ asset('storage/' . $media->file_path) }}"
                                         alt="{{ $media->alt_text ?? $media->file_name }}"
                                         class="w-full h-full object-cover hover:scale-105 transition-transform duration-300" />
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Pengingat Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-100">🔔 {{ __('Pengingat') }}</h3>
                    </div>
                    @if ($upcomingEvents->isEmpty())
                        <div class="text-center py-8">
                            <div class="text-4xl mb-3">📅</div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Tidak ada pengingat.') }}</p>
                            @if ($children->isNotEmpty())
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ __('Tambahkan jadwal imunisasi atau ulang tahun.') }}</p>
                            @endif
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach ($upcomingEvents as $event)
                                <a href="{{ route('calendar.show', [$event->child->slug, $event]) }}" class="block p-3 rounded-xl hover:bg-mintGreen-50 dark:hover:bg-mintGreen-950/20 transition group">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 rounded-xl {{ $event->event_type === 'immunization' ? 'bg-mintGreen-100 dark:bg-mintGreen-950/30' : ($event->event_type === 'birthday' ? 'bg-warmYellow-100 dark:bg-warmYellow-950/30' : 'bg-skyBlue-100 dark:bg-skyBlue-950/30') }} flex items-center justify-center text-lg flex-shrink-0 group-hover:scale-110 transition-transform">
                                            {{ $event->event_type === 'immunization' ? '💉' : ($event->event_type === 'birthday' ? '🎂' : '📅') }}
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">{{ $event->title }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $event->event_date->locale('id')->isoFormat('D MMM YYYY') }}</p>
                                        </div>
                                        <div class="text-xs text-gray-400 dark:text-gray-500 flex-shrink-0">
                                            {{ $event->event_date->locale('id')->diffForHumans() }}
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Second Row: Growth & Health -->
        <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Growth -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-100">📏 {{ __('Pertumbuhan Terbaru') }}</h3>
                    </div>
                    @if ($recentGrowths->isEmpty())
                        <div class="text-center py-6">
                            <div class="text-3xl mb-2">📏</div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Belum ada data pertumbuhan.') }}</p>
                            @if ($children->isNotEmpty())
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ __('Pantau tinggi dan berat badan buah hati.') }}</p>
                            @endif
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach ($recentGrowths as $growth)
                                <a href="{{ route('growth.index', $growth->child) }}" class="block p-3 rounded-xl hover:bg-mintGreen-50 dark:hover:bg-mintGreen-950/20 transition">
                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-mintGreen-400 to-skyBlue-400 flex items-center justify-center text-white text-xs flex-shrink-0">
                                            📏
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $growth->child->name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                @if($growth->weight_label) Berat: {{ $growth->weight_label }} @endif
                                                @if($growth->height_label) · Tinggi: {{ $growth->height_label }} @endif
                                                · {{ $growth->formatted_date }}
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Health Records -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-100">🏥 {{ __('Kesehatan Terbaru') }}</h3>
                    </div>
                    @if ($recentHealthRecords->isEmpty())
                        <div class="text-center py-6">
                            <div class="text-3xl mb-2">🏥</div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Belum ada catatan kesehatan.') }}</p>
                            @if ($children->isNotEmpty())
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ __('Catat riwayat imunisasi dan kesehatan.') }}</p>
                            @endif
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach ($recentHealthRecords as $record)
                                <a href="{{ route('health.show', [$record->child->slug, $record]) }}" class="block p-3 rounded-xl hover:bg-skyBlue-50 dark:hover:bg-skyBlue-950/20 transition">
                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-skyBlue-400 to-lavender-400 flex items-center justify-center text-white text-xs flex-shrink-0">
                                            {{ $record->type_icon }}
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $record->name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $record->child->name }} · {{ $record->type_label }} · {{ $record->formatted_date }}</p>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Access -->
        @if ($children->isNotEmpty())
            <div class="mt-8 bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl">
                <div class="p-6">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-100 mb-4">⚡ {{ __('Akses Cepat') }}</h3>
                    @php $firstChild = $children->first(); @endphp
                    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-3">
                        <a href="{{ route('children.index') }}" class="p-4 rounded-2xl bg-gradient-to-br from-softPink-50 to-lavender-50 dark:from-softPink-950/30 dark:to-lavender-950/30 border border-softPink-100 dark:border-softPink-900/30 hover:shadow-medium transition text-center group">
                            <div class="text-2xl mb-1 group-hover:scale-110 transition-transform">👶</div>
                            <div class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ __('Anak') }}</div>
                        </a>
                        <a href="{{ route('timeline.index', $firstChild) }}" class="p-4 rounded-2xl bg-gradient-to-br from-lavender-50 to-softPink-50 dark:from-lavender-950/30 dark:to-softPink-950/30 border border-lavender-100 dark:border-lavender-900/30 hover:shadow-medium transition text-center group">
                            <div class="text-2xl mb-1 group-hover:scale-110 transition-transform">📸</div>
                            <div class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ __('Timeline') }}</div>
                        </a>
                        <a href="{{ route('diaries.index', $firstChild) }}" class="p-4 rounded-2xl bg-gradient-to-br from-peach-50 to-warmYellow-50 dark:from-peach-950/30 dark:to-warmYellow-950/30 border border-peach-100 dark:border-peach-900/30 hover:shadow-medium transition text-center group">
                            <div class="text-2xl mb-1 group-hover:scale-110 transition-transform">📔</div>
                            <div class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ __('Diary') }}</div>
                        </a>
                        <a href="{{ route('calendar.index', $firstChild) }}" class="p-4 rounded-2xl bg-gradient-to-br from-mintGreen-50 to-skyBlue-50 dark:from-mintGreen-950/30 dark:to-skyBlue-950/30 border border-mintGreen-100 dark:border-mintGreen-900/30 hover:shadow-medium transition text-center group">
                            <div class="text-2xl mb-1 group-hover:scale-110 transition-transform">📅</div>
                            <div class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ __('Kalender') }}</div>
                        </a>
                        <a href="{{ route('growth.index', $firstChild) }}" class="p-4 rounded-2xl bg-gradient-to-br from-mintGreen-50 to-cream-50 dark:from-mintGreen-950/30 dark:to-gray-800 border border-mintGreen-100 dark:border-mintGreen-900/30 hover:shadow-medium transition text-center group">
                            <div class="text-2xl mb-1 group-hover:scale-110 transition-transform">📏</div>
                            <div class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ __('Pertumbuhan') }}</div>
                        </a>
                        <a href="{{ route('health.index', $firstChild) }}" class="p-4 rounded-2xl bg-gradient-to-br from-skyBlue-50 to-lavender-50 dark:from-skyBlue-950/30 dark:to-lavender-950/30 border border-skyBlue-100 dark:border-skyBlue-900/30 hover:shadow-medium transition text-center group">
                            <div class="text-2xl mb-1 group-hover:scale-110 transition-transform">🏥</div>
                            <div class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ __('Kesehatan') }}</div>
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
