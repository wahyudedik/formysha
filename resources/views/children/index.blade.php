<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                {{ __('Anak Saya') }}
            </h2>
            <a href="{{ route('children.create') }}" class="btn-primary text-sm">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('Tambah Anak') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 p-4 bg-mintGreen-50 dark:bg-mintGreen-950/30 border border-mintGreen-200 dark:border-mintGreen-800 text-mintGreen-700 dark:text-mintGreen-400 rounded-xl" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)">
                    {{ session('status') }}
                </div>
            @endif

            @if ($children->isEmpty())
                <!-- Empty State -->
                <div class="text-center py-16">
                    <div class="text-6xl mb-4">👶</div>
                    <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-2">{{ __('Belum Ada Profil Anak') }}</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-6">{{ __('Mulai dokumentasikan perjalanan hidup buah hati Anda.') }}</p>
                    <a href="{{ route('children.create') }}" class="btn-primary">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        {{ __('Tambah Anak Pertama') }}
                    </a>
                </div>
            @else
                <!-- Children Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($children as $child)
                        <a href="{{ route('children.show', $child) }}" class="card-hover block">
                            <div class="flex items-start gap-4">
                                <!-- Avatar -->
                                <div class="shrink-0">
                                    @if ($child->photo)
                                        <img src="{{ asset('storage/' . $child->photo) }}" alt="{{ $child->name }}" class="w-16 h-16 rounded-2xl object-cover shadow-soft" />
                                    @else
                                        <div class="w-16 h-16 rounded-2xl {{ $child->gender === 'female' ? 'bg-softPink-100 dark:bg-softPink-950/30' : 'bg-skyBlue-100 dark:bg-skyBlue-950/30' }} flex items-center justify-center text-2xl shadow-soft">
                                            {{ $child->gender === 'female' ? '👧' : '👦' }}
                                        </div>
                                    @endif
                                </div>

                                <!-- Info -->
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-bold text-lg text-gray-800 dark:text-gray-100 truncate">
                                        {{ $child->nickname ?? $child->name }}
                                    </h3>
                                    @if ($child->nickname)
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $child->name }}</p>
                                    @endif
                                    <div class="mt-2 flex flex-wrap gap-2 text-xs">
                                        <span class="inline-flex items-center px-2 py-1 rounded-lg {{ $child->gender === 'female' ? 'bg-softPink-100 dark:bg-softPink-950/30 text-softPink-600 dark:text-softPink-400' : 'bg-skyBlue-100 dark:bg-skyBlue-950/30 text-skyBlue-600 dark:text-skyBlue-400' }}">
                                            {{ $child->gender === 'female' ? 'Perempuan' : 'Laki-laki' }}
                                        </span>
                                        <span class="inline-flex items-center px-2 py-1 rounded-lg bg-lavender-100 dark:bg-lavender-950/30 text-lavender-600 dark:text-lavender-400">
                                            {{ $child->age ?? '-' }}
                                        </span>
                                        @if ($child->is_public)
                                            <span class="inline-flex items-center px-2 py-1 rounded-lg bg-mintGreen-100 dark:bg-mintGreen-950/30 text-mintGreen-600 dark:text-mintGreen-400">
                                                🌐 Publik
                                            </span>
                                        @endif
                                    </div>
                                    <p class="mt-2 text-xs text-gray-400 dark:text-gray-500">
                                        📅 Lahir: {{ $child->date_of_birth->format('d M Y') }}
                                    </p>
                                </div>
                            </div>

                            <!-- Stats -->
                            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                                <span>{{ $child->family_members_count ?? 0 }} anggota keluarga</span>
                                <span class="text-softPink-400 dark:text-softPink-300 font-medium">{{ __('Lihat detail →') }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
