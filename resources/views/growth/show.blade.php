<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => $child->name, 'url' => route('children.show', $child)],
            ['label' => 'Pertumbuhan', 'url' => route('growth.index', $child)],
            ['label' => $growth->formatted_date],
        ]" />
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mt-4">
            <x-page-header title="📏 Detail Pengukuran" subtitle="{{ $growth->formatted_date }}" />
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('growth.edit', [$child, $growth]) }}" class="inline-flex items-center justify-center px-4 py-2 bg-skyBlue-500 text-white rounded-xl hover:bg-skyBlue-600 transition text-sm font-medium min-h-[44px]">
                    ✏️ Edit
                </a>
                <form method="POST" action="{{ route('growth.destroy', [$child, $growth]) }}" x-data>
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-red-500 text-white rounded-xl hover:bg-red-600 transition text-sm font-medium min-h-[44px]" x-on:click.prevent="if(confirm('Yakin ingin menghapus data pengukuran ini?')) $el.closest('form').submit()">
                        🗑️ Hapus
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-child-nav :child="$child" active="growth" />

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">
                {{-- Measurement Details --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    {{-- Weight --}}
                    <div class="text-center p-4 bg-softPink-50 dark:bg-softPink-950/30 rounded-2xl">
                        <span class="text-2xl">⚖️</span>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">{{ __('Berat Badan') }}</p>
                        <p class="text-lg font-bold text-gray-800 dark:text-gray-100 mt-1">{{ $growth->weight_label ?? '—' }}</p>
                        @if ($assessment && $assessment['weightStatus'] !== 'unknown')
                            <span class="inline-block mt-1 px-2 py-0.5 text-xs rounded-full
                                {{ $assessment['weightStatus'] === 'normal' ? 'bg-mintGreen-100 dark:bg-mintGreen-950/30 text-mintGreen-600 dark:text-mintGreen-400' : '' }}
                                {{ $assessment['weightStatus'] === 'low' ? 'bg-warmYellow-100 dark:bg-warmYellow-950/30 text-warmYellow-600 dark:text-warmYellow-400' : '' }}
                                {{ $assessment['weightStatus'] === 'high' ? 'bg-softOrange-100 dark:bg-softOrange-950/30 text-softOrange-600 dark:text-softOrange-400' : '' }}
                                {{ $assessment['weightStatus'] === 'very_low' ? 'bg-red-100 dark:bg-red-950/30 text-red-600 dark:text-red-400' : '' }}
                                {{ $assessment['weightStatus'] === 'very_high' ? 'bg-red-100 dark:bg-red-950/30 text-red-600 dark:text-red-400' : '' }}">
                                {{ $assessment['weightStatus'] === 'normal' ? '✅ Normal' : '' }}
                                {{ $assessment['weightStatus'] === 'low' ? '⚠️ Rendah' : '' }}
                                {{ $assessment['weightStatus'] === 'high' ? '⚠️ Tinggi' : '' }}
                                {{ $assessment['weightStatus'] === 'very_low' ? '🔴 Sangat Rendah' : '' }}
                                {{ $assessment['weightStatus'] === 'very_high' ? '🔴 Sangat Tinggi' : '' }}
                            </span>
                        @endif
                    </div>

                    {{-- Height --}}
                    <div class="text-center p-4 bg-skyBlue-50 dark:bg-skyBlue-950/30 rounded-2xl">
                        <span class="text-2xl">📐</span>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">{{ __('Tinggi Badan') }}</p>
                        <p class="text-lg font-bold text-gray-800 dark:text-gray-100 mt-1">{{ $growth->height_label ?? '—' }}</p>
                        @if ($assessment && $assessment['heightStatus'] !== 'unknown')
                            <span class="inline-block mt-1 px-2 py-0.5 text-xs rounded-full
                                {{ $assessment['heightStatus'] === 'normal' ? 'bg-mintGreen-100 dark:bg-mintGreen-950/30 text-mintGreen-600 dark:text-mintGreen-400' : '' }}
                                {{ $assessment['heightStatus'] === 'low' ? 'bg-warmYellow-100 dark:bg-warmYellow-950/30 text-warmYellow-600 dark:text-warmYellow-400' : '' }}
                                {{ $assessment['heightStatus'] === 'high' ? 'bg-softOrange-100 dark:bg-softOrange-950/30 text-softOrange-600 dark:text-softOrange-400' : '' }}
                                {{ $assessment['heightStatus'] === 'very_low' ? 'bg-red-100 dark:bg-red-950/30 text-red-600 dark:text-red-400' : '' }}
                                {{ $assessment['heightStatus'] === 'very_high' ? 'bg-red-100 dark:bg-red-950/30 text-red-600 dark:text-red-400' : '' }}">
                                {{ $assessment['heightStatus'] === 'normal' ? '✅ Normal' : '' }}
                                {{ $assessment['heightStatus'] === 'low' ? '⚠️ Pendek' : '' }}
                                {{ $assessment['heightStatus'] === 'high' ? '⚠️ Tinggi' : '' }}
                                {{ $assessment['heightStatus'] === 'very_low' ? '🔴 Sangat Pendek' : '' }}
                                {{ $assessment['heightStatus'] === 'very_high' ? '🔴 Sangat Tinggi' : '' }}
                            </span>
                        @endif
                    </div>

                    {{-- Head Circumference --}}
                    <div class="text-center p-4 bg-lavender-50 dark:bg-lavender-950/30 rounded-2xl">
                        <span class="text-2xl">🧠</span>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">{{ __('Lingkar Kepala') }}</p>
                        <p class="text-lg font-bold text-gray-800 dark:text-gray-100 mt-1">{{ $growth->head_circumference_label ?? '—' }}</p>
                        @if ($assessment && $assessment['headStatus'] !== 'unknown')
                            <span class="inline-block mt-1 px-2 py-0.5 text-xs rounded-full
                                {{ $assessment['headStatus'] === 'normal' ? 'bg-mintGreen-100 dark:bg-mintGreen-950/30 text-mintGreen-600 dark:text-mintGreen-400' : '' }}
                                {{ $assessment['headStatus'] === 'low' ? 'bg-warmYellow-100 dark:bg-warmYellow-950/30 text-warmYellow-600 dark:text-warmYellow-400' : '' }}
                                {{ $assessment['headStatus'] === 'high' ? 'bg-softOrange-100 dark:bg-softOrange-950/30 text-softOrange-600 dark:text-softOrange-400' : '' }}
                                {{ $assessment['headStatus'] === 'very_low' ? 'bg-red-100 dark:bg-red-950/30 text-red-600 dark:text-red-400' : '' }}
                                {{ $assessment['headStatus'] === 'very_high' ? 'bg-red-100 dark:bg-red-950/30 text-red-600 dark:text-red-400' : '' }}">
                                {{ $assessment['headStatus'] === 'normal' ? '✅ Normal' : '' }}
                                {{ $assessment['headStatus'] === 'low' ? '⚠️ Kecil' : '' }}
                                {{ $assessment['headStatus'] === 'high' ? '⚠️ Besar' : '' }}
                                {{ $assessment['headStatus'] === 'very_low' ? '🔴 Sangat Kecil' : '' }}
                                {{ $assessment['headStatus'] === 'very_high' ? '🔴 Sangat Besar' : '' }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Notes --}}
                @if ($growth->notes)
                    <div>
                        <h4 class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide">{{ __('Catatan') }}</h4>
                        <p class="text-sm text-gray-800 dark:text-gray-200 mt-1">{{ $growth->notes }}</p>
                    </div>
                @endif

                {{-- WHO Assessment Info --}}
                @if ($assessment)
                    <div class="p-4 bg-cream-50 dark:bg-cream-950/30 rounded-xl border border-cream-100 dark:border-cream-900/30">
                        <h4 class="text-xs font-medium text-warmYellow-600 dark:text-warmYellow-400 uppercase tracking-wide mb-2">{{ __('Evaluasi Standar WHO') }}</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            {{ __('Penilaian berdasarkan standar pertumbuhan WHO untuk anak berusia') }}
                            <span class="font-medium">{{ $child->age ?? '—' }}</span>.
                            {{ __('Data dibandingkan dengan garis pertumbuhan median dan batas ±2 standar deviasi.') }}
                        </p>
                    </div>
                @endif
            </div>

            <div class="mt-6">
                <a href="{{ route('growth.index', $child) }}" class="text-sm text-skyBlue-600 hover:text-skyBlue-700 dark:text-skyBlue-400 dark:hover:text-skyBlue-300 font-medium">
                    ← {{ __('Kembali ke Daftar Pertumbuhan') }}
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
