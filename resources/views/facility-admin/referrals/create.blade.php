<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <x-breadcrumb :items="[
                    ['url' => route('facility.dashboard'), 'label' => __('Dashboard')],
                    ['url' => route('facility.referrals.index'), 'label' => __('Rujukan')],
                    ['label' => __('Buat Rujukan')],
                ]" />
                <h2 class="mt-2 font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                    🔄 {{ __('Buat Rujukan Baru') }}
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
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Data Rujukan') }}</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Rujukan pasien ke fasilitas kesehatan lain.') }}</p>
                        </div>

                        <form method="POST" action="{{ route('facility.referrals.store') }}" class="space-y-6" x-data="{ loading: false }" @submit="loading = true">
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

                            <!-- Target Facility -->
                            <div>
                                <x-input-label for="to_tenant_id" :value="__('Fasilitas Tujuan *')" />
                                <select id="to_tenant_id" name="to_tenant_id" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-softPink-300 focus:ring-softPink-200 rounded-xl shadow-sm transition" required>
                                    <option value="">{{ __('Pilih Fasilitas Tujuan') }}</option>
                                    @foreach ($facilities as $facility)
                                        <option value="{{ $facility->id }}" {{ old('to_tenant_id') == $facility->id ? 'selected' : '' }}>{{ $facility->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('to_tenant_id')" />
                            </div>

                            <!-- Reason -->
                            <div>
                                <x-input-label for="reason" :value="__('Alasan Rujukan *')" />
                                <textarea id="reason" name="reason" rows="3" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-softPink-300 focus:ring-softPink-200 rounded-xl shadow-sm transition" placeholder="{{ __('Jelaskan alasan rujukan...') }}" required>{{ old('reason') }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('reason')" />
                            </div>

                            <!-- Clinical Summary -->
                            <div>
                                <x-input-label for="clinical_summary" :value="__('Ringkasan Klinis')" />
                                <textarea id="clinical_summary" name="clinical_summary" rows="4" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-softPink-300 focus:ring-softPink-200 rounded-xl shadow-sm transition" placeholder="{{ __('Ringkasan kondisi klinis pasien...') }}">{{ old('clinical_summary') }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('clinical_summary')" />
                            </div>

                            <!-- Notes -->
                            <div>
                                <x-input-label for="notes" :value="__('Catatan Tambahan')" />
                                <textarea id="notes" name="notes" rows="2" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-softPink-300 focus:ring-softPink-200 rounded-xl shadow-sm transition" placeholder="{{ __('Catatan untuk fasilitas tujuan...') }}">{{ old('notes') }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('notes')" />
                            </div>

                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                                <button type="submit" x-bind:disabled="loading" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-softPink-500 hover:bg-softPink-600 text-white font-semibold rounded-xl shadow-soft transition-all duration-200 min-h-[44px] disabled:opacity-50 disabled:cursor-not-allowed">
                                    <svg x-show="loading" class="w-4 h-4 shrink-0 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    <span x-text="loading ? '{{ __('Mengirim...') }}' : '{{ __('Kirim Rujukan') }}'"></span>
                                </button>
                                <a href="{{ route('facility.referrals.index') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-xl transition min-h-[44px]">
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
