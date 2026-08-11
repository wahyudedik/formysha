<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('documents.index', $child) }}" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                📄 {{ __('Tambah Dokumen') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl">
                <div class="p-4 sm:p-6 lg:p-8">
                    <x-child-selector :children="$children" :child="$child" :route-name="'documents.create'" />

                    <!-- Child Info -->
                    <div class="flex items-center gap-3 mb-6 p-4 bg-gradient-to-r from-skyBlue-50 to-lavender-50 dark:from-skyBlue-950/30 dark:via-gray-800 dark:to-lavender-950/30 rounded-2xl">
                        <div class="w-10 h-10 rounded-xl {{ $child->gender === 'female' ? 'bg-softPink-100 dark:bg-softPink-950/30' : 'bg-skyBlue-100 dark:bg-skyBlue-950/30' }} flex items-center justify-center text-lg">
                            {{ $child->gender === 'female' ? '👧' : '👦' }}
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Dokumen untuk') }}</p>
                            <p class="font-semibold text-gray-800 dark:text-gray-100">{{ $child->nickname ?? $child->name }}</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('documents.store', $child) }}" enctype="multipart/form-data" x-data="{ loading: false }" @submit="loading = true">
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
                            <select id="type" name="type" class="mt-1 block w-full border-gray-300 dark:border-gray-600 focus:border-skyBlue-500 focus:ring-skyBlue-500 rounded-2xl shadow-soft dark:bg-gray-700 dark:text-gray-200" required>
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
                            <textarea id="description" name="description" rows="3" class="mt-1 block w-full border-gray-300 dark:border-gray-600 focus:border-skyBlue-500 focus:ring-skyBlue-500 rounded-2xl shadow-soft dark:bg-gray-700 dark:text-gray-200" placeholder="Catatan tambahan tentang dokumen ini...">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- File Upload -->
                        <div class="mb-5" x-data="{ fileName: '', fileSize: '', filePreview: '', fileCategory: '' }">
                            <x-input-label for="file" :value="__('File Dokumen')" />
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-2xl hover:border-skyBlue-400 transition bg-gradient-to-br from-skyBlue-50/50 to-lavender-50/50 dark:from-skyBlue-950/20 dark:to-lavender-950/20 cursor-pointer"
                                 @click="$refs.docFileInput.click()">
                                {{-- Empty State --}}
                                <div x-show="!fileName" class="space-y-2 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 24">
                                        <path d="M28 8H20a4 4 0 00-4 4v12a4 4 0 004 4h16a4 4 0 004-4V12a4 4 0 00-4-4h-8" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                        <path d="M24 16v-8m0 0l-3 3m3-3l3 3" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600 dark:text-gray-300 justify-center">
                                        <span class="font-medium text-skyBlue-600">{{ __('Pilih file') }}</span>
                                    </div>
                                    <p class="text-xs text-gray-400 dark:text-gray-500">PDF, JPG, PNG, DOC (Maks. 10MB)</p>
                                </div>
                                {{-- Preview State --}}
                                <div x-show="fileName" class="space-y-3 text-center">
                                    {{-- Image preview --}}
                                    <template x-if="fileCategory === 'image' && filePreview">
                                        <div class="relative inline-block">
                                            <img :src="filePreview" class="max-h-32 max-w-full rounded-xl shadow-soft object-contain mx-auto" :alt="fileName" />
                                            <div class="absolute -top-2 -right-2 bg-mintGreen-500 rounded-full p-1">
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </div>
                                        </div>
                                    </template>
                                    {{-- Non-image preview --}}
                                    <template x-if="fileCategory !== 'image'">
                                        <div class="flex flex-col items-center">
                                            <div class="w-16 h-16 rounded-2xl bg-skyBlue-100 dark:bg-skyBlue-950/30 flex items-center justify-center mb-2">
                                                <span class="text-3xl" x-text="fileCategory === 'pdf' ? '📕' : '📄'"></span>
                                            </div>
                                        </div>
                                    </template>
                                    <div>
                                        <p class="text-sm font-medium text-gray-700 dark:text-gray-200 truncate max-w-[250px]" x-text="fileName"></p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500" x-text="fileSize"></p>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-mintGreen-100 dark:bg-mintGreen-950/30 text-mintGreen-600 dark:text-mintGreen-400 text-xs font-medium">
                                        ✓ {{ __('Siap diunggah') }}
                                    </span>
                                </div>
                            </div>
                            <input id="file" name="file" type="file" class="sr-only" required accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                                   x-ref="docFileInput"
                                   @change="
                                       const file = $event.target.files[0];
                                       if (file) {
                                           fileName = file.name;
                                           const ext = file.name.split('.').pop().toLowerCase();
                                           if (['jpg','jpeg','png','gif','webp'].includes(ext)) {
                                               fileCategory = 'image';
                                               filePreview = URL.createObjectURL(file);
                                           } else if (ext === 'pdf') {
                                               fileCategory = 'pdf';
                                               filePreview = '';
                                           } else {
                                               fileCategory = 'file';
                                               filePreview = '';
                                           }
                                           if (file.size >= 1048576) fileSize = (file.size / 1048576).toFixed(2) + ' MB';
                                           else if (file.size >= 1024) fileSize = (file.size / 1024).toFixed(2) + ' KB';
                                           else fileSize = file.size + ' B';
                                       }
                                   "
                            >
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
                                <input type="checkbox" name="is_private" value="1" x-model="checked" class="rounded border-gray-300 dark:border-gray-600 text-skyBlue-500 focus:ring-skyBlue-500 dark:bg-gray-700">
                                <span class="text-sm text-gray-700 dark:text-gray-200">🔒 {{ __('Tandai sebagai privat') }}</span>
                            </label>
                        </div>

                        <!-- Submit -->
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                            <a href="{{ route('documents.index', $child) }}" class="btn-secondary min-h-[44px]">
                                {{ __('Batal') }}
                            </a>
                            <button type="submit" :disabled="loading" class="btn-primary min-h-[44px] disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg x-show="loading" class="w-4 h-4 mr-1 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <svg x-show="!loading" class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span x-text="loading ? '{{ __('Menyimpan...') }}' : '{{ __('Simpan Dokumen') }}'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
