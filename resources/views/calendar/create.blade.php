<x-app-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('calendar.index', $child) }}" class="text-sm text-skyBlue-600 hover:text-skyBlue-700 dark:text-skyBlue-400 dark:hover:text-skyBlue-300 transition">
                ← Kembali ke Kalender
            </a>
            <h2 class="mt-1 font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                📅 {{ __('Tambah Acara') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl">
            <div class="p-4 sm:p-6 lg:p-8">
                <x-child-selector :children="$children" :child="$child" :route-name="'calendar.create'" />

                <form method="POST" action="{{ route('calendar.store', $child) }}" x-data="{ loading: false }" @submit="loading = true">
                    @csrf

                    <!-- Child Info -->
                    <div class="mb-6 p-4 rounded-2xl bg-gradient-to-br from-mintGreen-50 to-skyBlue-50 dark:from-mintGreen-950/30 dark:to-skyBlue-950/30 border border-mintGreen-100 dark:border-mintGreen-900/30">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Acara untuk') }}</p>
                        <p class="font-semibold text-gray-800 dark:text-gray-100">{{ $child->nickname ?? $child->name }}</p>
                    </div>

                    <!-- Title -->
                    <div class="mb-5">
                        <x-input-label for="title" :value="__('Judul Acara')" />
                        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title')" required autofocus placeholder="Contoh: Imunisasi PCV 2" />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <!-- Event Type -->
                    <div class="mb-5">
                        <x-input-label for="event_type" :value="__('Jenis Acara')" />
                        <select id="event_type" name="event_type" class="mt-1 block w-full border-gray-300 dark:border-gray-600 focus:border-skyBlue-500 focus:ring-skyBlue-500 rounded-2xl shadow-soft dark:bg-gray-700 dark:text-gray-200" required>
                            <option value="">— Pilih Jenis —</option>
                            <option value="birthday" {{ old('event_type') === 'birthday' ? 'selected' : '' }}>🎂 Ulang Tahun</option>
                            <option value="immunization" {{ old('event_type') === 'immunization' ? 'selected' : '' }}>💉 Imunisasi</option>
                            <option value="appointment" {{ old('event_type') === 'appointment' ? 'selected' : '' }}>🩺 Janji Temu</option>
                            <option value="school" {{ old('event_type') === 'school' ? 'selected' : '' }}>🏫 Sekolah</option>
                            <option value="other" {{ old('event_type') === 'other' ? 'selected' : '' }}>📌 Lainnya</option>
                        </select>
                        <x-input-error :messages="$errors->get('event_type')" class="mt-2" />
                    </div>

                    <!-- Date & Time -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                        <div>
                            <x-input-label for="event_date" :value="__('Tanggal Acara')" />
                            <x-text-input id="event_date" name="event_date" type="date" class="mt-1 block w-full" :value="old('event_date', date('Y-m-d'))" required />
                            <x-input-error :messages="$errors->get('event_date')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="event_time" :value="__('Waktu (opsional)')" />
                            <x-text-input id="event_time" name="event_time" type="time" class="mt-1 block w-full" :value="old('event_time')" />
                            <x-input-error :messages="$errors->get('event_time')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-5">
                        <x-input-label for="description" :value="__('Deskripsi (opsional)')" />
                        <textarea id="description" name="description" rows="3" class="mt-1 block w-full border-gray-300 dark:border-gray-600 focus:border-skyBlue-500 focus:ring-skyBlue-500 rounded-2xl shadow-soft dark:bg-gray-700 dark:text-gray-200" placeholder="Detail acara...">{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <!-- Recurring -->
                    <div class="mb-5" x-data="{ isRecurring: {{ old('is_recurring') ? 'true' : 'false' }} }">
                        <label class="flex items-center gap-3 cursor-pointer mb-3">
                            <input type="hidden" name="is_recurring" value="0">
                            <input type="checkbox" name="is_recurring" value="1" x-model="isRecurring" class="rounded border-gray-300 dark:border-gray-600 text-skyBlue-500 focus:ring-skyBlue-500 dark:bg-gray-700">
                            <span class="text-sm text-gray-700 dark:text-gray-200">🔁 {{ __('Acara berulang') }}</span>
                        </label>
                        <div x-show="isRecurring" x-transition class="ml-6">
                            <x-input-label for="recurrence_pattern" :value="__('Pola Pengulangan')" />
                            <select id="recurrence_pattern" name="recurrence_pattern" class="mt-1 block w-full border-gray-300 dark:border-gray-600 focus:border-skyBlue-500 focus:ring-skyBlue-500 rounded-2xl shadow-soft dark:bg-gray-700 dark:text-gray-200">
                                <option value="weekly" {{ old('recurrence_pattern') === 'weekly' ? 'selected' : '' }}>Setiap Minggu</option>
                                <option value="monthly" {{ old('recurrence_pattern') === 'monthly' ? 'selected' : '' }}>Setiap Bulan</option>
                                <option value="yearly" {{ old('recurrence_pattern') === 'yearly' ? 'selected' : '' }}>Setiap Tahun</option>
                            </select>
                            <x-input-error :messages="$errors->get('recurrence_pattern')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Reminder -->
                    <div class="mb-6">
                        <x-input-label for="reminder_at" :value="__('Pengingat (opsional)')" />
                        <x-text-input id="reminder_at" name="reminder_at" type="datetime-local" class="mt-1 block w-full" :value="old('reminder_at')" />
                        <x-input-error :messages="$errors->get('reminder_at')" class="mt-2" />
                    </div>

                    <!-- Submit -->
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                        <a href="{{ route('calendar.index', $child) }}" class="btn-secondary min-h-[44px]">
                            Batal
                        </a>
                        <button type="submit" :disabled="loading" class="btn-primary min-h-[44px] disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg x-show="loading" class="w-4 h-4 mr-1 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <svg x-show="!loading" class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span x-text="loading ? '{{ __('Menyimpan...') }}' : '{{ __('Simpan Acara') }}'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
