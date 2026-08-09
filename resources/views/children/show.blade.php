<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <a href="{{ route('children.index') }}" class="shrink-0 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight truncate">
                    {{ $child->nickname ?? $child->name }}
                </h2>
            </div>
            <a href="{{ route('children.edit', $child) }}" class="shrink-0 inline-flex items-center justify-center gap-1 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-xl text-sm hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                {{ __('Edit') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12 has-bottom-nav">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-4 lg:gap-8">
                <x-child-nav :child="$child" />

                <div class="flex-1 space-y-6 min-w-0">
            @if (session('status'))
                <div class="p-4 bg-mintGreen-50 dark:bg-mintGreen-950/30 border border-mintGreen-200 dark:border-mintGreen-800 text-mintGreen-700 dark:text-mintGreen-400 rounded-xl" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Profile Header Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl">
                <div class="bg-gradient-to-br from-softPink-50 via-cream-50 to-lavender-50 dark:from-softPink-950/30 dark:via-gray-800 dark:to-lavender-950/30 p-4 sm:p-6 lg:p-8">
                    <div class="flex flex-col sm:flex-row items-center sm:items-start gap-4 sm:gap-6">
                        @if ($child->photo)
                            <img src="{{ asset('storage/' . $child->photo) }}" alt="{{ $child->name }}" class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl object-cover shadow-soft-md" />
                        @else
                            <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl {{ $child->gender === 'female' ? 'bg-softPink-100 dark:bg-softPink-950/30' : 'bg-skyBlue-100 dark:bg-skyBlue-950/30' }} flex items-center justify-center text-3xl sm:text-4xl shadow-soft-md">
                                {{ $child->gender === 'female' ? '👧' : '👦' }}
                            </div>
                        @endif
                        <div class="text-center sm:text-left">
                            <h3 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $child->name }}</h3>
                            @if ($child->nickname)
                                <p class="text-gray-500 dark:text-gray-400">Panggilan: {{ $child->nickname }}</p>
                            @endif
                            <div class="mt-2 flex flex-wrap gap-2 justify-center sm:justify-start">
                                <span class="inline-flex items-center px-3 py-1 rounded-xl text-sm {{ $child->gender === 'female' ? 'bg-softPink-100 dark:bg-softPink-950/30 text-softPink-600 dark:text-softPink-400' : 'bg-skyBlue-100 dark:bg-skyBlue-950/30 text-skyBlue-600 dark:text-skyBlue-400' }}">
                                    {{ $child->gender === 'female' ? '👧 Perempuan' : '👦 Laki-laki' }}
                                </span>
                                @if ($child->is_public)
                                    <span class="inline-flex items-center px-3 py-1 rounded-xl text-sm bg-mintGreen-100 dark:bg-mintGreen-950/30 text-mintGreen-600 dark:text-mintGreen-400">
                                        🌐 Profil Publik
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Info -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl p-6">
                    <h4 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">📋 Informasi Dasar</h4>
                    <div class="space-y-3">
                        <div class="flex justify-between gap-2">
                            <span class="text-gray-500 dark:text-gray-400 shrink-0">Tanggal Lahir</span>
                            <span class="font-medium text-gray-800 dark:text-gray-100 text-right">{{ $child->date_of_birth->format('d M Y') }}</span>
                        </div>
                        <div class="flex justify-between gap-2">
                            <span class="text-gray-500 dark:text-gray-400 shrink-0">Usia</span>
                            <span class="font-medium text-gray-800 dark:text-gray-100 text-right">{{ $child->age ?? '-' }}</span>
                        </div>
                        @if ($child->place_of_birth)
                            <div class="flex justify-between gap-2">
                                <span class="text-gray-500 dark:text-gray-400 shrink-0">Tempat Lahir</span>
                                <span class="font-medium text-gray-800 dark:text-gray-100 text-right">{{ $child->place_of_birth }}</span>
                            </div>
                        @endif
                        @if ($child->blood_type)
                            <div class="flex justify-between gap-2">
                                <span class="text-gray-500 dark:text-gray-400 shrink-0">Golongan Darah</span>
                                <span class="font-medium text-gray-800 dark:text-gray-100 text-right">{{ $child->blood_type }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Family Members -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-bold text-gray-800 dark:text-gray-100">👨‍👩‍👧‍👦 Keluarga</h4>
                        <a href="{{ route('family.index', $child) }}" class="text-sm text-softPink-400 hover:text-softPink-600 dark:text-softPink-300 dark:hover:text-softPink-200 font-medium transition">
                            Lihat Semua →
                        </a>
                    </div>
                    @if ($child->familyMembers->isEmpty())
                        <p class="text-gray-400 dark:text-gray-500 text-sm">Belum ada anggota keluarga.</p>
                    @else
                        <div class="space-y-3">
                            @foreach ($child->familyMembers->take(4) as $member)
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-lavender-100 dark:bg-lavender-950/30 flex items-center justify-center text-sm">
                                        {{ match($member->relationship) {
                                            'father' => '👨',
                                            'mother' => '👩',
                                            'grandfather' => '👴',
                                            'grandmother' => '👵',
                                            default => '👤',
                                        } }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">{{ $member->name }}</p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500">{{ $member->relationship_label }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Bio -->
            @if ($child->bio)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl p-6">
                    <h4 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-3">💜 Tentang {{ $child->nickname ?? $child->name }}</h4>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed">{{ $child->bio }}</p>
                </div>
            @endif

            <!-- Export Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl p-6">
                <h4 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-3">📄 {{ __('Export Data') }}</h4>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ __('Unduh data profil dan riwayat dalam format PDF atau ZIP.') }}</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <a href="{{ route('export.profile', $child) }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-softPink-100 hover:bg-softPink-200 dark:bg-softPink-950/30 dark:hover:bg-softPink-900/40 text-softPink-700 dark:text-softPink-400 font-medium rounded-xl text-sm transition-all duration-200">
                        <svg class="w-4 h-4 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        {{ __('Profil Anak') }}
                    </a>
                    <a href="{{ route('export.health', $child) }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-mintGreen-100 hover:bg-mintGreen-200 dark:bg-mintGreen-950/30 dark:hover:bg-mintGreen-900/40 text-mintGreen-700 dark:text-mintGreen-400 font-medium rounded-xl text-sm transition-all duration-200">
                        <svg class="w-4 h-4 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                        {{ __('Riwayat Kesehatan') }}
                    </a>
                    <a href="{{ route('export.growth', $child) }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-skyBlue-100 hover:bg-skyBlue-200 dark:bg-skyBlue-950/30 dark:hover:bg-skyBlue-900/40 text-skyBlue-700 dark:text-skyBlue-400 font-medium rounded-xl text-sm transition-all duration-200">
                        <svg class="w-4 h-4 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        {{ __('Riwayat Pertumbuhan') }}
                    </a>
                    <a href="{{ route('export.zip', $child) }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-warmYellow-100 hover:bg-warmYellow-200 dark:bg-warmYellow-950/30 dark:hover:bg-warmYellow-900/40 text-warmYellow-700 dark:text-warmYellow-400 font-medium rounded-xl text-sm transition-all duration-200">
                        <svg class="w-4 h-4 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                        </svg>
                        {{ __('Semua Data (ZIP)') }}
                    </a>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl p-6">
                <h4 class="text-lg font-bold text-red-600 dark:text-red-400 mb-3">⚠️ Zona Berbahaya</h4>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Menghapus profil anak akan menghapus semua data terkait secara permanen.</p>
                <form method="POST" action="{{ route('children.destroy', $child) }}" x-data="{ confirming: false }" @submit.prevent="if(confirm('Apakah Anda yakin ingin menghapus profil {{ $child->name }}? Semua data akan hilang secara permanen.')) $el.submit();">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center justify-center gap-1 px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-xl shadow-soft transition-all duration-200 text-sm min-h-[44px]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        {{ __('Hapus Profil') }}
                    </button>
                </form>
            </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
