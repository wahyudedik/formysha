<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => $child->name, 'url' => route('children.show', $child)],
            ['label' => 'Kesehatan', 'url' => route('health.index', $child)],
            ['label' => $healthRecord->name],
        ]" />
        <div class="flex items-center justify-between mt-4">
            <x-page-header title="{{ $healthRecord->type_icon }} {{ $healthRecord->name }}" subtitle="{{ $healthRecord->type_label }} — {{ $healthRecord->formatted_date }}" />
            <div class="flex items-center gap-2">
                <a href="{{ route('health.edit', [$child, $healthRecord]) }}" class="px-4 py-2 bg-skyBlue-500 text-white rounded-xl hover:bg-skyBlue-600 transition text-sm font-medium">
                    ✏️ Edit
                </a>
                <form method="POST" action="{{ route('health.destroy', [$child, $healthRecord]) }}" x-data>
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-xl hover:bg-red-600 transition text-sm font-medium" x-on:click.prevent="if(confirm('Yakin ingin menghapus catatan ini?')) $el.closest('form').submit()">
                        🗑️ Hapus
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-child-nav :child="$child" active="health" />

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
                {{-- Type Badge --}}
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $healthRecord->type_color }}">
                        {{ $healthRecord->type_icon }} {{ $healthRecord->type_label }}
                    </span>
                </div>

                {{-- Details --}}
                <div class="space-y-4">
                    <div>
                        <h4 class="text-xs font-medium text-gray-400 uppercase tracking-wide">Tanggal</h4>
                        <p class="text-sm text-gray-800 mt-1">{{ $healthRecord->formatted_date }}</p>
                    </div>

                    @if ($healthRecord->description)
                        <div>
                            <h4 class="text-xs font-medium text-gray-400 uppercase tracking-wide">Deskripsi</h4>
                            <p class="text-sm text-gray-800 mt-1">{{ $healthRecord->description }}</p>
                        </div>
                    @endif

                    @if ($healthRecord->doctor)
                        <div>
                            <h4 class="text-xs font-medium text-gray-400 uppercase tracking-wide">Dokter</h4>
                            <p class="text-sm text-gray-800 mt-1">🩺 {{ $healthRecord->doctor }}</p>
                        </div>
                    @endif

                    @if ($healthRecord->hospital)
                        <div>
                            <h4 class="text-xs font-medium text-gray-400 uppercase tracking-wide">Rumah Sakit / Klinik</h4>
                            <p class="text-sm text-gray-800 mt-1">🏥 {{ $healthRecord->hospital }}</p>
                        </div>
                    @endif

                    @if ($healthRecord->notes)
                        <div>
                            <h4 class="text-xs font-medium text-gray-400 uppercase tracking-wide">Catatan</h4>
                            <p class="text-sm text-gray-800 mt-1">{{ $healthRecord->notes }}</p>
                        </div>
                    @endif

                    @if ($healthRecord->next_date)
                        <div class="p-4 bg-warmYellow-50 rounded-xl border border-warmYellow-100">
                            <h4 class="text-xs font-medium text-warmYellow-600 uppercase tracking-wide">Jadwal Berikutnya</h4>
                            <p class="text-sm text-warmYellow-700 mt-1 font-medium">📅 {{ $healthRecord->formatted_next_date }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="mt-6">
                <a href="{{ route('health.index', $child) }}" class="text-sm text-skyBlue-600 hover:text-skyBlue-700 font-medium">
                    ← Kembali ke Daftar Kesehatan
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
