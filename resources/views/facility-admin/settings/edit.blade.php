<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <x-breadcrumb :items="[
                    ['url' => route('facility.dashboard'), 'label' => __('Dashboard')],
                    ['label' => __('Pengaturan')],
                ]" />
                <h2 class="mt-2 font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                    ⚙️ {{ __('Pengaturan Fasilitas') }}
                </h2>
            </div>
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

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl p-4 sm:p-6 lg:p-8">
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Informasi Fasilitas') }}</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Perbarui data fasilitas kesehatan Anda.') }}</p>
                        </div>

                        <form method="POST" action="{{ route('facility.settings.update') }}" class="space-y-6" x-data="{ loading: false }" @submit="loading = true">
                            @csrf
                            @method('PUT')

                            <!-- Facility Name -->
                            <div>
                                <x-input-label for="name" :value="__('Nama Fasilitas *')" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $tenant->name)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>

                            <!-- Address -->
                            <div>
                                <x-input-label for="address" :value="__('Alamat')" />
                                <textarea id="address" name="address" rows="2" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-softPink-300 focus:ring-softPink-200 rounded-xl shadow-sm transition">{{ old('address', $tenant->address) }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('address')" />
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- City -->
                                <div>
                                    <x-input-label for="city" :value="__('Kota')" />
                                    <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" :value="old('city', $facility->city ?? '')" />
                                    <x-input-error class="mt-2" :messages="$errors->get('city')" />
                                </div>

                                <!-- Province -->
                                <div>
                                    <x-input-label for="province" :value="__('Provinsi')" />
                                    <x-text-input id="province" name="province" type="text" class="mt-1 block w-full" :value="old('province', $facility->province ?? '')" />
                                    <x-input-error class="mt-2" :messages="$errors->get('province')" />
                                </div>
                            </div>

                            <!-- Postal Code -->
                            <div class="max-w-xs">
                                <x-input-label for="postal_code" :value="__('Kode Pos')" />
                                <x-text-input id="postal_code" name="postal_code" type="text" class="mt-1 block w-full" :value="old('postal_code', $facility->postal_code ?? '')" />
                                <x-input-error class="mt-2" :messages="$errors->get('postal_code')" />
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- Phone -->
                                <div>
                                    <x-input-label for="phone" :value="__('Telepon')" />
                                    <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $tenant->phone)" />
                                    <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                                </div>

                                <!-- Email -->
                                <div>
                                    <x-input-label for="email_institution" :value="__('Email Institusi')" />
                                    <x-text-input id="email_institution" name="email_institution" type="email" class="mt-1 block w-full" :value="old('email_institution', $tenant->email_institution)" />
                                    <x-input-error class="mt-2" :messages="$errors->get('email_institution')" />
                                </div>
                            </div>

                            <!-- Website -->
                            <div>
                                <x-input-label for="website" :value="__('Website')" />
                                <x-text-input id="website" name="website" type="url" class="mt-1 block w-full" :value="old('website', $tenant->website)" placeholder="https://" />
                                <x-input-error class="mt-2" :messages="$errors->get('website')" />
                            </div>

                            <!-- License Number -->
                            <div>
                                <x-input-label for="license_number" :value="__('Nomor Izin')" />
                                <x-text-input id="license_number" name="license_number" type="text" class="mt-1 block w-full" :value="old('license_number', $tenant->license_number)" />
                                <x-input-error class="mt-2" :messages="$errors->get('license_number')" />
                            </div>

                            <!-- Description -->
                            <div>
                                <x-input-label for="description" :value="__('Deskripsi')" />
                                <textarea id="description" name="description" rows="3" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-softPink-300 focus:ring-softPink-200 rounded-xl shadow-sm transition">{{ old('description', $tenant->description) }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('description')" />
                            </div>

                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                                <button type="submit" x-bind:disabled="loading" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-softPink-500 hover:bg-softPink-600 text-white font-semibold rounded-xl shadow-soft transition-all duration-200 min-h-[44px] disabled:opacity-50 disabled:cursor-not-allowed">
                                    <svg x-show="loading" class="w-4 h-4 shrink-0 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    <span x-text="loading ? '{{ __('Menyimpan...') }}' : '{{ __('Simpan Perubahan') }}'"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
