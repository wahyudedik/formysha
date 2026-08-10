<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <x-breadcrumb :items="[
                    ['url' => route('facility.dashboard'), 'label' => __('Dashboard')],
                    ['label' => __('Daftar Pasien')],
                ]" />
                <h2 class="mt-2 font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                    👶 {{ __('Daftar Pasien') }}
                </h2>
            </div>
            <a href="{{ route('facility.patients.create') }}" class="inline-flex items-center justify-center gap-1 px-4 py-2.5 bg-softPink-500 hover:bg-softPink-600 text-white font-medium rounded-xl text-sm transition shadow-soft min-h-[44px]">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('Tambah Pasien') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-6">
                @include('facility-admin.partials.sidebar')
                <div class="flex-1 min-w-0">
                    @if (session('success'))
                        <div class="mb-6 p-4 bg-mintGreen-50 dark:bg-mintGreen-950/30 border border-mintGreen-200 dark:border-mintGreen-800 text-mintGreen-700 dark:text-mintGreen-400 rounded-xl" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($patientLinks->isEmpty())
                        <x-empty-state
                            icon="👶"
                            :title="__('Belum Ada Pasien')"
                            :description="__('Daftarkan pasien pertama untuk mulai mengelola data kesehatan.')"
                            :action-url="route('facility.patients.create')"
                            :action-text="__('Daftarkan Pasien Pertama')"
                        />
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach ($patientLinks as $link)
                                <a href="{{ route('facility.patients.show', $link) }}" class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft p-5 border border-gray-100 dark:border-gray-700 hover:shadow-md transition group block">
                                    <div class="flex items-start gap-3">
                                        <div class="w-12 h-12 rounded-xl bg-softPink-100 dark:bg-softPink-950/30 flex items-center justify-center text-xl shrink-0">
                                            👶
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="font-bold text-gray-800 dark:text-gray-100 truncate group-hover:text-softPink-600 dark:group-hover:text-softPink-400 transition">
                                                {{ $link->child->name ?? '-' }}
                                            </h3>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                                {{ __('Orang tua') }}: {{ $link->parentUser->name ?? '-' }}
                                            </p>
                                            <div class="mt-2 flex items-center gap-2">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-medium {{ $link->status->value === 'active' ? 'bg-mintGreen-100 dark:bg-mintGreen-950/30 text-mintGreen-700 dark:text-mintGreen-400' : ($link->status->value === 'pending' ? 'bg-warmYellow-100 dark:bg-warmYellow-950/30 text-warmYellow-700 dark:text-warmYellow-400' : 'bg-red-100 dark:bg-red-950/30 text-red-700 dark:text-red-400') }}">
                                                    {{ $link->status->label() }}
                                                </span>
                                                <span class="text-xs text-gray-400 dark:text-gray-500 font-mono">
                                                    {{ $link->link_code }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $patientLinks->withQueryString()->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
