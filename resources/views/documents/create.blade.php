<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('documents.index', $child) }}" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                📄 {{ __('Tambah Dokumen') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-soft sm:rounded-3xl">
                <div class="p-6 sm:p-8">
                    <!-- Child Info -->
                    <div class="flex items-center gap-3 mb-6 p-4 bg-gradient-to-r from-skyBlue-50 to-lavender-50 rounded-2xl">
                        <div class="w-10 h-10 rounded-xl {{ $child->gender === 'female' ? 'bg-softPink-100' : 'bg-skyBlue-100' }} flex items-center justify-center text-lg">
                            {{ $child->gender === 'female' ? '👧' : '👦' }}
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">{{ __('Dokumen untuk') }}</p>
                            <p class="font-semibold text-gray-800">{{ $child->nickname ?? $child->name }}</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('documents.store', $child) }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Name -->
                        <div class="mb-5">
                            <x-input-label for="name" :value="__('Nama Dokumen')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus placeholder="Contoh: Akta Lahir Mysha" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Type -->
                        <div class="mb-5">
                            <x-input-label for="type" :value="__('Jenis Dokumen')" />
                            <select id="type" name="type" class="mt-1 block w-full border-gray-300 focus:border-skyBlue-500 focus:ring-skyBlue-500 rounded-2xl shadow-soft" required>
                                <option value="">{{ __('Pilih jenis dokumen...') }}</option>
                                <option value="birth_certificate" {{ old('type') === 'birth_certificate' ? 'selected' : '' }}>📜 Akta Lahir</option>
                                <option value="family_card" {{ old('type') === 'family_card' ? 'selected' : '' }}>🏠 Kartu Keluarga</option>
                                <option value="kia" {{ old('type') === 'kia' ? 'selected' : '' }}>🪪 KIA</option>
                                <option value="bpjs" {{ old('type') === 'bpjs' ? 'selected' : '' }}>🏥 BPJS</option>
                                <option value="passport" {{ old('type') === 'passport' ? 'selected' : '' }}>✈️ Paspor</option>
                                <option value="certificate" {{ old('type') === 'certificate' ? 'selected' : '' }}>🎓 Sertifikat</option>
                                <option value="report_card" {{ old('type') === 'report_card' ? 'selected' : '' }}>📋 Rapor</option>
                                <option value="other" {{ old('type') === 'other' ? 'selected' : '' }}>📄 Lainnya</option>
                            </select>
                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div class="mb-5">
                            <x-input-label for="description" :value="__('Deskripsi (opsional)')" />
                            <textarea id="description" name="description" rows="3" class="mt-1 block w-full border-gray-300 focus:border-skyBlue-500 focus:ring-skyBlue-500 rounded-2xl shadow-soft" placeholder="Catatan tambahan tentang dokumen ini...">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- File Upload -->
                        <div class="mb-5">
                            <x-input-label for="file" :value="__('File Dokumen')" />
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-dashed border-gray-300 rounded-2xl hover:border-skyBlue-400 transition bg-gradient-to-br from-skyBlue-50/50 to-lavender-50/50">
                                <div class="space-y-2 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 24">
                                        <path d="M28 8H20a4 4 0 00-4 4v12a4 4 0 004 4h16a4 4 0 004-4V12a4 4 0 00-4-4h-8" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                        <path d="M24 16v-8m0 0l-3 3m3-3l3 3" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600 justify-center">
                                        <label for="file" class="relative cursor-pointer rounded-xl font-medium text-skyBlue-600 hover:text-skyBlue-500">
                                            <span>{{ __('Pilih file') }}</span>
                                            <input id="file" name="file" type="file" class="sr-only" required accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                        </label>
                                    </div>
                                    <p class="text-xs text-gray-400">PDF, JPG, PNG, DOC (Maks. 10MB)</p>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('file')" class="mt-2" />
                        </div>

                        <!-- Dates -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                            <div>
                                <x-input-label for="issued_date" :value="__('Tanggal Terbit (opsional)')" />
                                <x-text-input id="issued_date" name="issued_date" type="date" class="mt-1 block w-full" :value="old('issued_date')" />
                                <x-input-error :messages="$errors->get('issued_date')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="expiry_date" :value="__('Tanggal Kedaluwarsa (opsional)')" />
                                <x-text-input id="expiry_date" name="expiry_date" type="date" class="mt-1 block w-full" :value="old('expiry_date')" />
                                <x-input-error :messages="$errors->get('expiry_date')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Privacy -->
                        <div class="mb-6" x-data="{ checked: {{ old('is_private', 'true') === 'true' || old('is_private') === '1' ? 'true' : 'false' }} }">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="hidden" name="is_private" value="0">
                                <input type="checkbox" name="is_private" value="1" x-model="checked" class="rounded border-gray-300 text-skyBlue-500 focus:ring-skyBlue-500">
                                <span class="text-sm text-gray-700">🔒 {{ __('Tandai sebagai privat') }}</span>
                            </label>
                        </div>

                        <!-- Submit -->
                        <div class="flex items-center gap-3">
                            <a href="{{ route('documents.index', $child) }}" class="btn-secondary">
                                {{ __('Batal') }}
                            </a>
                            <button type="submit" class="btn-primary">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ __('Simpan Dokumen') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
