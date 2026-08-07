<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('children.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Tambah Anak Baru') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-soft sm:rounded-3xl p-6 sm:p-8">
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900">{{ __('Profil Anak') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ __('Isi informasi dasar buah hati Anda.') }}</p>
                </div>

                <form method="POST" action="{{ route('children.store') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- Name -->
                    <div>
                        <x-input-label for="name" :value="__('Nama Lengkap *')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus placeholder="Contoh: Mysha Aisyah" />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <!-- Nickname -->
                    <div>
                        <x-input-label for="nickname" :value="__('Nama Panggilan')" />
                        <x-text-input id="nickname" name="nickname" type="text" class="mt-1 block w-full" :value="old('nickname')" placeholder="Contoh: Mysha" />
                        <x-input-error class="mt-2" :messages="$errors->get('nickname')" />
                    </div>

                    <!-- Gender -->
                    <div>
                        <x-input-label for="gender" :value="__('Jenis Kelamin *')" />
                        <select id="gender" name="gender" class="mt-1 block w-full border-gray-300 focus:border-softPink-300 focus:ring-softPink-200 rounded-xl shadow-sm transition" required>
                            <option value="">{{ __('Pilih Jenis Kelamin') }}</option>
                            <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>👦 Laki-laki</option>
                            <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>👧 Perempuan</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('gender')" />
                    </div>

                    <!-- Date of Birth -->
                    <div>
                        <x-input-label for="date_of_birth" :value="__('Tanggal Lahir *')" />
                        <x-text-input id="date_of_birth" name="date_of_birth" type="date" class="mt-1 block w-full" :value="old('date_of_birth')" required />
                        <x-input-error class="mt-2" :messages="$errors->get('date_of_birth')" />
                    </div>

                    <!-- Place of Birth -->
                    <div>
                        <x-input-label for="place_of_birth" :value="__('Tempat Lahir')" />
                        <x-text-input id="place_of_birth" name="place_of_birth" type="text" class="mt-1 block w-full" :value="old('place_of_birth')" placeholder="Contoh: Jakarta" />
                        <x-input-error class="mt-2" :messages="$errors->get('place_of_birth')" />
                    </div>

                    <!-- Blood Type -->
                    <div>
                        <x-input-label for="blood_type" :value="__('Golongan Darah')" />
                        <select id="blood_type" name="blood_type" class="mt-1 block w-full border-gray-300 focus:border-softPink-300 focus:ring-softPink-200 rounded-xl shadow-sm transition">
                            <option value="">{{ __('Pilih Golongan Darah') }}</option>
                            <option value="A" {{ old('blood_type') === 'A' ? 'selected' : '' }}>A</option>
                            <option value="B" {{ old('blood_type') === 'B' ? 'selected' : '' }}>B</option>
                            <option value="AB" {{ old('blood_type') === 'AB' ? 'selected' : '' }}>AB</option>
                            <option value="O" {{ old('blood_type') === 'O' ? 'selected' : '' }}>O</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('blood_type')" />
                    </div>

                    <!-- Bio -->
                    <div>
                        <x-input-label for="bio" :value="__('Cerita Singkat')" />
                        <textarea id="bio" name="bio" rows="3" class="mt-1 block w-full border-gray-300 focus:border-softPink-300 focus:ring-softPink-200 rounded-xl shadow-sm transition" placeholder="Ceritakan sedikit tentang buah hati Anda...">{{ old('bio') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('bio')" />
                    </div>

                    <!-- Photo -->
                    <div>
                        <x-input-label for="photo" :value="__('Foto Profil')" />
                        <div class="mt-1 flex items-center gap-4">
                            <div x-data="{ preview: null }" class="flex items-center gap-4">
                                <div class="w-20 h-20 rounded-full bg-softPink-100 flex items-center justify-center overflow-hidden">
                                    <img x-show="preview" :src="preview" class="w-20 h-20 object-cover" alt="Preview" />
                                    <svg x-show="!preview" class="w-8 h-8 text-softPink-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div>
                                    <input type="file" id="photo" name="photo" accept="image/*" class="hidden" x-ref="photoInput" @change="preview = URL.createObjectURL($refs.photoInput.files[0])" />
                                    <button type="button" @click="$refs.photoInput.click()" class="text-sm text-softPink-500 hover:text-softPink-600 font-medium transition">
                                        {{ __('Pilih Foto') }}
                                    </button>
                                    <p class="text-xs text-gray-400 mt-1">{{ __('JPG/PNG, maks 2MB') }}</p>
                                </div>
                            </div>
                        </div>
                        <x-input-error class="mt-2" :messages="$errors->get('photo')" />
                    </div>

                    <!-- Submit -->
                    <div class="flex items-center gap-4 pt-4">
                        <button type="submit" class="btn-primary">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            {{ __('Simpan') }}
                        </button>
                        <a href="{{ route('children.index') }}" class="btn-secondary">
                            {{ __('Batal') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
