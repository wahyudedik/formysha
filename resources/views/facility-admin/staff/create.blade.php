<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <x-breadcrumb :items="[
                    ['url' => route('facility.dashboard'), 'label' => __('Dashboard')],
                    ['url' => route('facility.staff.index'), 'label' => __('Staf')],
                    ['label' => __('Tambah Staf')],
                ]" />
                <h2 class="mt-2 font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                    👨‍⚕️ {{ __('Tambah Staf Baru') }}
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
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Data Staf') }}</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Isi data staf yang akan ditambahkan.') }}</p>
                        </div>

                        <form method="POST" action="{{ route('facility.staff.store') }}" class="space-y-6" x-data="{ loading: false }" @submit="loading = true">
                            @csrf

                            <!-- Name -->
                            <div>
                                <x-input-label for="name" :value="__('Nama Lengkap *')" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" placeholder="{{ __('Nama lengkap staf') }}" required />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>

                            <!-- Email -->
                            <div>
                                <x-input-label for="email" :value="__('Email *')" />
                                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" placeholder="{{ __('Email staf') }}" required />
                                <x-input-error class="mt-2" :messages="$errors->get('email')" />
                            </div>

                            <!-- Role -->
                            <div>
                                <x-input-label for="staff_role" :value="__('Role *')" />
                                <select id="staff_role" name="staff_role" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-softPink-300 focus:ring-softPink-200 rounded-xl shadow-sm transition" required>
                                    <option value="">{{ __('Pilih Role') }}</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->value }}" {{ old('staff_role') == $role->value ? 'selected' : '' }}>{{ $role->label() }}</option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('staff_role')" />
                            </div>

                            <!-- Specialization -->
                            <div>
                                <x-input-label for="specialization" :value="__('Spesialisasi')" />
                                <x-text-input id="specialization" name="specialization" type="text" class="mt-1 block w-full" :value="old('specialization')" placeholder="{{ __('Contoh: Anak, Kandungan, Umum') }}" />
                                <x-input-error class="mt-2" :messages="$errors->get('specialization')" />
                            </div>

                            <!-- License Number -->
                            <div>
                                <x-input-label for="license_number" :value="__('No. STR/SIP')" />
                                <x-text-input id="license_number" name="license_number" type="text" class="mt-1 block w-full" :value="old('license_number')" placeholder="{{ __('Nomor registrasi profesi') }}" />
                                <x-input-error class="mt-2" :messages="$errors->get('license_number')" />
                            </div>

                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                                <button type="submit" x-bind:disabled="loading" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-skyBlue-500 hover:bg-skyBlue-600 text-white font-semibold rounded-xl shadow-soft transition-all duration-200 min-h-[44px] disabled:opacity-50 disabled:cursor-not-allowed">
                                    <svg x-show="loading" class="w-4 h-4 shrink-0 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    <span x-text="loading ? '{{ __('Menyimpan...') }}' : '{{ __('Simpan Staf') }}'"></span>
                                </button>
                                <a href="{{ route('facility.staff.index') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-xl transition min-h-[44px]">
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
