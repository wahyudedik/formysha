<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('children.show', $child) }}" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Keluarga') }} {{ $child->nickname ?? $child->name }}
                </h2>
            </div>
            <a href="{{ route('family.create', $child) }}" class="btn-primary text-sm">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('Tambah') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 p-4 bg-mintGreen-50 border border-mintGreen-200 text-mintGreen-700 rounded-xl" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)">
                    {{ session('status') }}
                </div>
            @endif

            @if ($familyMembers->isEmpty())
                <!-- Empty State -->
                <div class="text-center py-16">
                    <div class="text-6xl mb-4">👨‍👩‍👧‍👦</div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">{{ __('Belum Ada Anggota Keluarga') }}</h3>
                    <p class="text-gray-500 mb-6">{{ __('Tambahkan anggota keluarga untuk ') . ($child->nickname ?? $child->name) . '.' }}</p>
                    <a href="{{ route('family.create', $child) }}" class="btn-primary">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        {{ __('Tambah Anggota Keluarga') }}
                    </a>
                </div>
            @else
                <!-- Family Members List -->
                <div class="space-y-4">
                    @foreach ($familyMembers as $member)
                        <div class="bg-white overflow-hidden shadow-soft sm:rounded-2xl p-6 flex items-center gap-4">
                            <!-- Avatar -->
                            <div class="shrink-0">
                                @if ($member->photo)
                                    <img src="{{ asset('storage/' . $member->photo) }}" alt="{{ $member->name }}" class="w-14 h-14 rounded-2xl object-cover shadow-soft" />
                                @else
                                    <div class="w-14 h-14 rounded-2xl bg-lavender-100 flex items-center justify-center text-2xl shadow-soft">
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
                            </div>

                            <!-- Info -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <h3 class="font-bold text-gray-800 truncate">{{ $member->name }}</h3>
                                    @if ($member->is_primary)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-medium bg-warmYellow-100 text-warmYellow-600">
                                            ⭐ Utama
                                        </span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-500">{{ $member->relationship_label }}</p>
                                @if ($member->phone)
                                    <p class="text-xs text-gray-400 mt-1">📱 {{ $member->phone }}</p>
                                @endif
                                @if ($member->email)
                                    <p class="text-xs text-gray-400 mt-1">📧 {{ $member->email }}</p>
                                @endif
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center gap-2 shrink-0">
                                <a href="{{ route('family.edit', [$child, $member]) }}" class="p-2 text-gray-400 hover:text-softPink-500 hover:bg-softPink-50 rounded-xl transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('family.destroy', [$child, $member]) }}" x-data="{ confirming: false }" @submit.prevent="if(confirm('Apakah Anda yakin ingin menghapus {{ addslashes($member->name) }}?')) $el.submit();">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-xl transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
