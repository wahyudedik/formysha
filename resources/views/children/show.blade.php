<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('children.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $child->nickname ?? $child->name }}
                </h2>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('children.edit', $child) }}" class="btn-secondary text-sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    {{ __('Edit') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="p-4 bg-mintGreen-50 border border-mintGreen-200 text-mintGreen-700 rounded-xl" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Profile Header Card -->
            <div class="bg-white overflow-hidden shadow-soft sm:rounded-3xl">
                <div class="bg-gradient-to-br from-softPink-50 via-cream-50 to-lavender-50 p-6 sm:p-8">
                    <div class="flex items-center gap-6">
                        @if ($child->photo)
                            <img src="{{ asset('storage/' . $child->photo) }}" alt="{{ $child->name }}" class="w-24 h-24 rounded-2xl object-cover shadow-soft-md" />
                        @else
                            <div class="w-24 h-24 rounded-2xl {{ $child->gender === 'female' ? 'bg-softPink-100' : 'bg-skyBlue-100' }} flex items-center justify-center text-4xl shadow-soft-md">
                                {{ $child->gender === 'female' ? '👧' : '👦' }}
                            </div>
                        @endif
                        <div>
                            <h3 class="text-2xl font-bold text-gray-800">{{ $child->name }}</h3>
                            @if ($child->nickname)
                                <p class="text-gray-500">Panggilan: {{ $child->nickname }}</p>
                            @endif
                            <div class="mt-2 flex flex-wrap gap-2">
                                <span class="inline-flex items-center px-3 py-1 rounded-xl text-sm {{ $child->gender === 'female' ? 'bg-softPink-100 text-softPink-600' : 'bg-skyBlue-100 text-skyBlue-600' }}">
                                    {{ $child->gender === 'female' ? '👧 Perempuan' : '👦 Laki-laki' }}
                                </span>
                                @if ($child->is_public)
                                    <span class="inline-flex items-center px-3 py-1 rounded-xl text-sm bg-mintGreen-100 text-mintGreen-600">
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
                <div class="bg-white overflow-hidden shadow-soft sm:rounded-3xl p-6">
                    <h4 class="text-lg font-bold text-gray-800 mb-4">📋 Informasi Dasar</h4>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Tanggal Lahir</span>
                            <span class="font-medium text-gray-800">{{ $child->date_of_birth->format('d M Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Usia</span>
                            <span class="font-medium text-gray-800">{{ $child->age ?? '-' }}</span>
                        </div>
                        @if ($child->place_of_birth)
                            <div class="flex justify-between">
                                <span class="text-gray-500">Tempat Lahir</span>
                                <span class="font-medium text-gray-800">{{ $child->place_of_birth }}</span>
                            </div>
                        @endif
                        @if ($child->blood_type)
                            <div class="flex justify-between">
                                <span class="text-gray-500">Golongan Darah</span>
                                <span class="font-medium text-gray-800">{{ $child->blood_type }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Family Members -->
                <div class="bg-white overflow-hidden shadow-soft sm:rounded-3xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-bold text-gray-800">👨‍👩‍👧‍👦 Keluarga</h4>
                        <a href="{{ route('family.index', $child) }}" class="text-sm text-softPink-400 hover:text-softPink-600 font-medium transition">
                            Lihat Semua →
                        </a>
                    </div>
                    @if ($child->familyMembers->isEmpty())
                        <p class="text-gray-400 text-sm">Belum ada anggota keluarga.</p>
                    @else
                        <div class="space-y-3">
                            @foreach ($child->familyMembers->take(4) as $member)
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-lavender-100 flex items-center justify-center text-sm">
                                        {{ match($member->relationship) {
                                            'father' => '👨',
                                            'mother' => '👩',
                                            'grandfather' => '👴',
                                            'grandmother' => '👵',
                                            default => '👤',
                                        } }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-800">{{ $member->name }}</p>
                                        <p class="text-xs text-gray-400">{{ $member->relationship_label }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Bio -->
            @if ($child->bio)
                <div class="bg-white overflow-hidden shadow-soft sm:rounded-3xl p-6">
                    <h4 class="text-lg font-bold text-gray-800 mb-3">💜 Tentang {{ $child->nickname ?? $child->name }}</h4>
                    <p class="text-gray-600 leading-relaxed">{{ $child->bio }}</p>
                </div>
            @endif

            <!-- Danger Zone -->
            <div class="bg-white overflow-hidden shadow-soft sm:rounded-3xl p-6">
                <h4 class="text-lg font-bold text-red-600 mb-3">⚠️ Zona Berbahaya</h4>
                <p class="text-sm text-gray-500 mb-4">Menghapus profil anak akan menghapus semua data terkait secara permanen.</p>
                <form method="POST" action="{{ route('children.destroy', $child) }}" x-data="{ confirming: false }" @submit.prevent="if(confirm('Apakah Anda yakin ingin menghapus profil {{ $child->name }}? Semua data akan hilang secara permanen.')) $el.submit();">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-xl shadow-soft transition-all duration-200 text-sm">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        {{ __('Hapus Profil') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
