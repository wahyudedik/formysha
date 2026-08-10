<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <x-breadcrumb :items="[
                    ['url' => route('facility.dashboard'), 'label' => __('Dashboard')],
                    ['url' => route('facility.clinical-notes.index'), 'label' => __('Catatan Klinis')],
                    ['label' => __('Tambah Catatan')],
                ]" />
                <h2 class="mt-2 font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                    📋 {{ __('Tambah Catatan Klinis') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-6">
                @include('facility-admin.partials.sidebar')
                <div class="flex-1 min-w-0">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl p-4 sm:p-6 lg:p-8">
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Data Catatan Klinis') }}</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Isi detail catatan klinis untuk pasien.') }}</p>
                        </div>

                        <form method="POST" action="{{ route('facility.clinical-notes.store') }}" class="space-y-6" x-data="{ loading: false }" @submit="loading = true">
                            @csrf

                            <!-- Child (Patient) -->
                            <div>
                                <x-input-label for="child_id" :value="__('Pasien (Anak) *')" />
                                <select id="child_id" name="child_id" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-softPink-300 focus:ring-softPink-200 rounded-xl shadow-sm transition" required>
                                    <option value="">{{ __('Pilih Pasien') }}</option>
                                    @foreach ($children as $child)
                                        <option value="{{ $child->id }}" {{ old('child_id') == $child->id ? 'selected' : '' }}>{{ $child->name }} — {{ $child->date_of_birth?->format('d M Y') ?? '-' }}</option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('child_id')" />
                            </div>

                            <!-- Staff -->
                            <div>
                                <x-input-label for="staff_user_id" :value="__('Staf Penulis *')" />
                                <select id="staff_user_id" name="staff_user_id" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-softPink-300 focus:ring-softPink-200 rounded-xl shadow-sm transition" required>
                                    <option value="">{{ __('Pilih Staf') }}</option>
                                    @foreach ($staffMembers as $staff)
                                        <option value="{{ $staff->user_id }}" {{ old('staff_user_id') == $staff->user_id ? 'selected' : '' }}>{{ $staff->user->name ?? '-' }} ({{ $staff->staff_role->label() }})</option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('staff_user_id')" />
                            </div>

                            <!-- Type -->
                            <div>
                                <x-input-label for="type" :value="__('Tipe Catatan *')" />
                                <select id="type" name="type" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-softPink-300 focus:ring-softPink-200 rounded-xl shadow-sm transition" required>
                                    <option value="">{{ __('Pilih Tipe') }}</option>
                                    @foreach ($types as $type)
                                        <option value="{{ $type->value }}" {{ old('type') == $type->value ? 'selected' : '' }}>{{ $type->label() }}</option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('type')" />
                            </div>

                            <!-- Title -->
                            <div>
                                <x-input-label for="title" :value="__('Judul *')" />
                                <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title')" placeholder="{{ __('Contoh: Pemeriksaan rutin bulanan') }}" required />
                                <x-input-error class="mt-2" :messages="$errors->get('title')" />
                            </div>

                            <!-- Content -->
                            <div>
                                <x-input-label for="content" :value="__('Isi Catatan *')" />
                                <textarea id="content" name="content" rows="6" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-softPink-300 focus:ring-softPink-200 rounded-xl shadow-sm transition" placeholder="{{ __('Tuliskan detail catatan klinis...') }}" required>{{ old('content') }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('content')" />
                            </div>

                            <!-- Diagnosis -->
                            <div>
                                <x-input-label for="diagnosis" :value="__('Diagnosis')" />
                                <x-text-input id="diagnosis" name="diagnosis" type="text" class="mt-1 block w-full" :value="old('diagnosis')" placeholder="{{ __('Contoh: Sehat, tidak ada keluhan') }}" />
                                <x-input-error class="mt-2" :messages="$errors->get('diagnosis')" />
                            </div>

                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                                <button type="submit" x-bind:disabled="loading" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-softPink-500 hover:bg-softPink-600 text-white font-semibold rounded-xl shadow-soft transition-all duration-200 min-h-[44px] disabled:opacity-50 disabled:cursor-not-allowed">
                                    <svg x-show="loading" class="w-4 h-4 shrink-0 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    <span x-text="loading ? '{{ __('Menyimpan...') }}' : '{{ __('Simpan Catatan') }}'"></span>
                                </button>
                                <a href="{{ route('facility.clinical-notes.index') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-xl transition min-h-[44px]">
                                    {{ __('Batal') }}
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
