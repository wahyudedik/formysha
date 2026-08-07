<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            🏠 {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Welcome Section -->
        <div class="mb-8 bg-gradient-to-br from-softPink-50 to-lavender-50 rounded-3xl p-6 sm:p-8 border border-softPink-100">
            <h1 class="text-2xl font-bold text-gray-800">{{ __('Selamat datang, ') }}{{ auth()->user()->name }}! 👋</h1>
            <p class="mt-2 text-gray-600">{{ __('Kelola perjalanan hidup buah hati Anda di ForMysha.') }}</p>
        </div>

        <!-- Children Cards -->
        @if ($children->isEmpty())
            <div class="mb-8 bg-white overflow-hidden shadow-soft sm:rounded-3xl">
                <div class="p-8 text-center">
                    <div class="text-5xl mb-4">👶</div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">{{ __('Belum Ada Anak') }}</h3>
                    <p class="text-gray-500 mb-6">{{ __('Mulai dokumentasikan perjalanan hidup buah hati Anda.') }}</p>
                    <a href="{{ route('children.create') }}" class="btn-primary">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        {{ __('Tambah Anak') }}
                    </a>
                </div>
            </div>
        @else
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">👶 {{ __('Anak Saya') }}</h3>
                    <a href="{{ route('children.create') }}" class="text-sm text-skyBlue-600 hover:text-skyBlue-700 transition">
                        + {{ __('Tambah') }}
                    </a>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($children as $child)
                        <a href="{{ route('children.show', $child) }}" class="block card-hover p-5 rounded-2xl bg-white border border-gray-100 hover:shadow-medium transition-all duration-200">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-softPink-400 to-lavender-400 flex items-center justify-center text-white text-lg font-bold flex-shrink-0">
                                    {{ strtoupper(substr($child->name, 0, 1)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-semibold text-gray-800 truncate">{{ $child->nickname ?? $child->name }}</h4>
                                    <p class="text-xs text-gray-500">{{ $child->age ?? '—' }}</p>
                                </div>
                            </div>
                            <div class="mt-4 grid grid-cols-3 gap-2 text-center">
                                <div class="p-2 rounded-xl bg-lavender-50">
                                    <div class="text-sm font-bold text-lavender-700">{{ $child->timelines_count ?? 0 }}</div>
                                    <div class="text-[10px] text-gray-500">Timeline</div>
                                </div>
                                <div class="p-2 rounded-xl bg-peach-50">
                                    <div class="text-sm font-bold text-peach-700">{{ $child->diaries_count ?? 0 }}</div>
                                    <div class="text-[10px] text-gray-500">Diary</div>
                                </div>
                                <div class="p-2 rounded-xl bg-skyBlue-50">
                                    <div class="text-sm font-bold text-skyBlue-700">{{ $child->documents_count ?? 0 }}</div>
                                    <div class="text-[10px] text-gray-500">Dokumen</div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Recent Timelines -->
            <div class="bg-white overflow-hidden shadow-soft sm:rounded-3xl">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-gray-800">📸 {{ __('Timeline Terbaru') }}</h3>
                    </div>
                    @if ($recentTimelines->isEmpty())
                        <div class="text-center py-6">
                            <div class="text-3xl mb-2">📸</div>
                            <p class="text-sm text-gray-500">{{ __('Belum ada timeline.') }}</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach ($recentTimelines as $timeline)
                                <a href="{{ route('timeline.show', [$timeline->child_id, $timeline->id]) }}" class="block p-3 rounded-xl hover:bg-lavender-50 transition">
                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-lavender-400 to-softPink-400 flex items-center justify-center text-white text-xs flex-shrink-0">
                                            📸
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-gray-800 truncate">{{ $timeline->title }}</p>
                                            <p class="text-xs text-gray-500">{{ $timeline->child->name }} · {{ \Carbon\Carbon::parse($timeline->event_date)->locale('id')->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Upcoming Events -->
            <div class="bg-white overflow-hidden shadow-soft sm:rounded-3xl">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-gray-800">📅 {{ __('Acara Mendatang') }}</h3>
                    </div>
                    @if ($upcomingEvents->isEmpty())
                        <div class="text-center py-6">
                            <div class="text-3xl mb-2">📅</div>
                            <p class="text-sm text-gray-500">{{ __('Tidak ada acara mendatang.') }}</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach ($upcomingEvents as $event)
                                <a href="{{ route('calendar.show', [$event->child_id, $event->id]) }}" class="block p-3 rounded-xl hover:bg-mintGreen-50 transition">
                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-mintGreen-400 to-skyBlue-400 flex items-center justify-center text-white text-xs flex-shrink-0">
                                            📅
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-gray-800 truncate">{{ $event->title }}</p>
                                            <p class="text-xs text-gray-500">{{ $event->child->name }} · {{ \Carbon\Carbon::parse($event->event_date)->locale('id')->isoFormat('D MMM YYYY') }}</p>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Diaries -->
            <div class="bg-white overflow-hidden shadow-soft sm:rounded-3xl">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-gray-800">📔 {{ __('Diary Terbaru') }}</h3>
                    </div>
                    @if ($recentDiaries->isEmpty())
                        <div class="text-center py-6">
                            <div class="text-3xl mb-2">📔</div>
                            <p class="text-sm text-gray-500">{{ __('Belum ada diary.') }}</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach ($recentDiaries as $diary)
                                <a href="{{ route('diaries.show', [$diary->child_id, $diary->id]) }}" class="block p-3 rounded-xl hover:bg-peach-50 transition">
                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-peach-400 to-warmYellow-400 flex items-center justify-center text-white text-xs flex-shrink-0">
                                            📔
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-gray-800 truncate">{{ $diary->title }}</p>
                                            <p class="text-xs text-gray-500">{{ $diary->child->name }} · {{ $diary->mood_label }} · {{ \Carbon\Carbon::parse($diary->diary_date)->locale('id')->diffForHumans() }}</p>
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
            <div class="bg-white overflow-hidden shadow-soft sm:rounded-3xl">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-gray-800">📏 {{ __('Pertumbuhan Terbaru') }}</h3>
                    </div>
                    @if ($recentGrowths->isEmpty())
                        <div class="text-center py-6">
                            <div class="text-3xl mb-2">📏</div>
                            <p class="text-sm text-gray-500">{{ __('Belum ada data pertumbuhan.') }}</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach ($recentGrowths as $growth)
                                <div class="p-3 rounded-xl hover:bg-mintGreen-50 transition">
                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-mintGreen-400 to-skyBlue-400 flex items-center justify-center text-white text-xs flex-shrink-0">
                                            📏
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-medium text-gray-800">{{ $growth->child->name }}</p>
                                            <p class="text-xs text-gray-500">
                                                @if($growth->weight_label) Berat: {{ $growth->weight_label }} @endif
                                                @if($growth->height_label) · Tinggi: {{ $growth->height_label }} @endif
                                                · {{ $growth->formatted_date }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Health Records -->
            <div class="bg-white overflow-hidden shadow-soft sm:rounded-3xl">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-gray-800">🏥 {{ __('Kesehatan Terbaru') }}</h3>
                    </div>
                    @if ($recentHealthRecords->isEmpty())
                        <div class="text-center py-6">
                            <div class="text-3xl mb-2">🏥</div>
                            <p class="text-sm text-gray-500">{{ __('Belum ada catatan kesehatan.') }}</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach ($recentHealthRecords as $record)
                                <a href="{{ route('health.show', [$record->child_id, $record]) }}" class="block p-3 rounded-xl hover:bg-skyBlue-50 transition">
                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-skyBlue-400 to-lavender-400 flex items-center justify-center text-white text-xs flex-shrink-0">
                                            {{ $record->type_icon }}
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-medium text-gray-800">{{ $record->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $record->child->name }} · {{ $record->type_label }} · {{ $record->formatted_date }}</p>
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
            <div class="mt-8 bg-white overflow-hidden shadow-soft sm:rounded-3xl">
                <div class="p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">⚡ {{ __('Akses Cepat') }}</h3>
                    @php $firstChild = $children->first(); @endphp
                    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-3">
                        <a href="{{ route('children.index') }}" class="p-4 rounded-2xl bg-gradient-to-br from-softPink-50 to-lavender-50 border border-softPink-100 hover:shadow-medium transition text-center">
                            <div class="text-2xl mb-1">👶</div>
                            <div class="text-xs font-medium text-gray-700">{{ __('Anak') }}</div>
                        </a>
                        <a href="{{ route('timeline.index', $firstChild) }}" class="p-4 rounded-2xl bg-gradient-to-br from-lavender-50 to-softPink-50 border border-lavender-100 hover:shadow-medium transition text-center">
                            <div class="text-2xl mb-1">📸</div>
                            <div class="text-xs font-medium text-gray-700">{{ __('Timeline') }}</div>
                        </a>
                        <a href="{{ route('diaries.index', $firstChild) }}" class="p-4 rounded-2xl bg-gradient-to-br from-peach-50 to-warmYellow-50 border border-peach-100 hover:shadow-medium transition text-center">
                            <div class="text-2xl mb-1">📔</div>
                            <div class="text-xs font-medium text-gray-700">{{ __('Diary') }}</div>
                        </a>
                        <a href="{{ route('calendar.index', $firstChild) }}" class="p-4 rounded-2xl bg-gradient-to-br from-mintGreen-50 to-skyBlue-50 border border-mintGreen-100 hover:shadow-medium transition text-center">
                            <div class="text-2xl mb-1">📅</div>
                            <div class="text-xs font-medium text-gray-700">{{ __('Kalender') }}</div>
                        </a>
                        <a href="{{ route('growth.index', $firstChild) }}" class="p-4 rounded-2xl bg-gradient-to-br from-mintGreen-50 to-cream-50 border border-mintGreen-100 hover:shadow-medium transition text-center">
                            <div class="text-2xl mb-1">📏</div>
                            <div class="text-xs font-medium text-gray-700">{{ __('Pertumbuhan') }}</div>
                        </a>
                        <a href="{{ route('health.index', $firstChild) }}" class="p-4 rounded-2xl bg-gradient-to-br from-skyBlue-50 to-lavender-50 border border-skyBlue-100 hover:shadow-medium transition text-center">
                            <div class="text-2xl mb-1">🏥</div>
                            <div class="text-xs font-medium text-gray-700">{{ __('Kesehatan') }}</div>
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
