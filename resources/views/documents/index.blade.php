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
                    📄 {{ __('My Documents') }} — {{ $child->nickname ?? $child->name }}
                </h2>
            </div>
            <a href="{{ route('documents.create', $child) }}" class="btn-primary text-sm">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('Tambah Dokumen') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 p-4 bg-mintGreen-50 border border-mintGreen-200 text-mintGreen-700 rounded-xl" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)">
                    {{ session('status') }}
                </div>
            @endif

            @if ($documents->isEmpty())
                <!-- Empty State -->
                <div class="text-center py-16">
                    <div class="text-6xl mb-4">📋</div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">{{ __('Belum Ada Dokumen') }}</h3>
                    <p class="text-gray-500 mb-6">{{ __('Simpan dokumen penting ' . ($child->nickname ?? $child->name) . ' di sini.') }}</p>
                    <a href="{{ route('documents.create', $child) }}" class="btn-primary">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        {{ __('Tambah Dokumen Pertama') }}
                    </a>
                </div>
            @else
                <!-- Documents Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($documents as $doc)
                        <a href="{{ route('documents.show', [$child, $doc]) }}" class="card-hover block">
                            <div class="flex items-start gap-4">
                                <!-- Icon -->
                                <div class="shrink-0">
                                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-skyBlue-50 to-lavender-50 flex items-center justify-center text-2xl shadow-soft">
                                        @if ($doc->type === 'birth_certificate') 📜
                                        @elseif ($doc->type === 'family_card') 🏠
                                        @elseif ($doc->type === 'kia') 🪪
                                        @elseif ($doc->type === 'bpjs') 🏥
                                        @elseif ($doc->type === 'passport') ✈️
                                        @elseif ($doc->type === 'certificate') 🎓
                                        @elseif ($doc->type === 'report_card') 📋
                                        @else 📄
                                        @endif
                                    </div>
                                </div>

                                <!-- Info -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-2">
                                        <h3 class="font-semibold text-gray-800 truncate">{{ $doc->name }}</h3>
                                        @if ($doc->is_private)
                                            <span class="shrink-0 text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-500">🔒</span>
                                        @else
                                            <span class="shrink-0 text-xs px-2 py-0.5 rounded-full bg-mintGreen-100 text-mintGreen-600">🌐</span>
                                        @endif
                                    </div>

                                    <p class="mt-1 text-sm text-gray-500">{{ $doc->type_label }}</p>

                                    @if ($doc->description)
                                        <p class="mt-1 text-xs text-gray-400 line-clamp-1">{{ $doc->description }}</p>
                                    @endif

                                    <div class="mt-2 flex flex-wrap items-center gap-3 text-xs text-gray-400">
                                        <span>📎 {{ $doc->formatted_size }}</span>
                                        @if ($doc->expiry_date)
                                            @if ($doc->is_expired)
                                                <span class="text-red-500 font-medium">⚠️ Kedaluwarsa</span>
                                            @else
                                                <span>📅 Hingga {{ $doc->formatted_expiry_date }}</span>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $documents->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
