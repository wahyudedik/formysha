<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <x-breadcrumb :items="[
                    ['label' => 'Dashboard', 'url' => route('dashboard')],
                    ['label' => $child->name, 'url' => route('children.show', $child)],
                    ['label' => 'Milestone'],
                ]" />
                <h1 class="text-2xl font-bold text-gray-900">🎯 Milestone & Pengingat</h1>
                <p class="mt-1 text-sm text-gray-500">Pencapaian dan pengingat penting untuk {{ $child->name }}</p>
            </div>
            <form method="POST" action="{{ route('milestones.check', $child) }}">
                @csrf
                <button type="submit"
                    x-data="{ loading: false }"
                    @submit="loading = true"
                    :disabled="loading"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-mintGreen text-white rounded-xl hover:bg-mintGreen/90 transition-colors min-h-[44px]">
                    <svg x-show="loading" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    <span x-text="loading ? 'Mengecek...' : '🔄 Cek Milestone'"></span>
                </button>
            </form>
        </div>
    </x-slot>

    <div class="px-4 sm:px-6 lg:px-8 py-8">
        {{-- Summary --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
            <div class="bg-white rounded-2xl p-4 sm:p-6 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-skyBlue/20 flex items-center justify-center text-lg">🎯</div>
                    <div>
                        <p class="text-sm text-gray-500">Aktif</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalActive }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-4 sm:p-6 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-mintGreen/20 flex items-center justify-center text-lg">✅</div>
                    <div>
                        <p class="text-sm text-gray-500">Ditutup</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $dismissed->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-4 sm:p-6 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-softPink/20 flex items-center justify-center text-lg">📅</div>
                    <div>
                        <p class="text-sm text-gray-500">Hari Ini</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $milestones->where('milestone_date', now()->toDateString())->count() }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Active Milestones --}}
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">🎯 Milestone Aktif</h2>

            @if($milestones->isEmpty())
                <x-empty-state
                    icon="🎯"
                    title="Belum ada milestone aktif"
                    description="Milestone akan muncul secara otomatis saat ada pencapaian atau pengingat untuk {{ $child->name }}."
                />
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($milestones as $milestone)
                        <div class="bg-white rounded-2xl p-4 sm:p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <span class="text-2xl">{{ $milestone->icon ?? '📌' }}</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-skyBlue/20 text-skyBlue-700">
                                        {{ \App\Models\MilestoneAlert::TYPES[$milestone->type]['name'] ?? $milestone->type }}
                                    </span>
                                </div>
                                <form method="POST" action="{{ route('milestones.dismiss', [$child, $milestone]) }}">
                                    @csrf
                                    <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors p-1" title="Tutup">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </form>
                            </div>
                            <h3 class="font-semibold text-gray-900 mb-1">{{ $milestone->title }}</h3>
                            <p class="text-sm text-gray-500 mb-3">{{ $milestone->description }}</p>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-400">
                                    {{ $milestone->milestone_date->format('d M Y') }}
                                </span>
                                <span class="inline-flex items-center text-xs font-medium
                                    {{ $milestone->milestone_date->isToday() ? 'text-red-600' : ($milestone->milestone_date->isPast() ? 'text-gray-400' : 'text-mintGreen-700') }}">
                                    {{ $milestone->days_until_label }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Dismissed Milestones --}}
        @if($dismissed->isNotEmpty())
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">📋 Milestone Ditutup</h2>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="divide-y divide-gray-100">
                        @foreach($dismissed as $item)
                            <div class="px-4 sm:px-6 py-3 flex items-center justify-between opacity-60">
                                <div class="flex items-center gap-3">
                                    <span class="text-lg">{{ $item->icon ?? '📌' }}</span>
                                    <div>
                                        <p class="text-sm font-medium text-gray-700">{{ $item->title }}</p>
                                        <p class="text-xs text-gray-400">{{ $item->milestone_date->format('d M Y') }}</p>
                                    </div>
                                </div>
                                <span class="text-xs text-gray-400">Ditutup</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
