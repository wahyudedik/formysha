<x-app-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('calendar.show', [$child, $event]) }}" class="text-sm text-skyBlue-600 hover:text-skyBlue-700 dark:text-skyBlue-400 dark:hover:text-skyBlue-300 transition">
                ← Kembali ke Detail Acara
            </a>
            <h2 class="mt-1 font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                ✏️ {{ __('Edit Acara') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-soft sm:rounded-3xl">
            <div class="p-4 sm:p-6 lg:p-8">
                <form method="POST" action="{{ route('calendar.update', [$child, $event]) }}">
                    @csrf
                    @method('PUT')

                    <!-- Title -->
                    <div class="mb-5">
                        <x-input-label for="title" :value="__('Judul Acara')" />
                        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $event->title)" required autofocus />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <!-- Event Type -->
                    <div class="mb-5">
                        <x-input-label for="event_type" :value="__('Jenis Acara')" />
                        <select id="event_type" name="event_type" class="mt-1 block w-full border-gray-300 dark:border-gray-600 focus:border-skyBlue-500 focus:ring-skyBlue-500 rounded-2xl shadow-soft dark:bg-gray-700 dark:text-gray-200" required>
                            <option value="birthday" {{ old('event_type', $event->event_type) === 'birthday' ? 'selected' : '' }}>🎂 Ulang Tahun</option>
                            <option value="immunization" {{ old('event_type', $event->event_type) === 'immunization' ? 'selected' : '' }}>💉 Imunisasi</option>
                            <option value="appointment" {{ old('event_type', $event->event_type) === 'appointment' ? 'selected' : '' }}>🩺 Janji Temu</option>
                            <option value="school" {{ old('event_type', $event->event_type) === 'school' ? 'selected' : '' }}>🏫 Sekolah</option>
                            <option value="other" {{ old('event_type', $event->event_type) === 'other' ? 'selected' : '' }}>📌 Lainnya</option>
                        </select>
                        <x-input-error :messages="$errors->get('event_type')" class="mt-2" />
                    </div>

                    <!-- Date & Time -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                        <div>
                            <x-input-label for="event_date" :value="__('Tanggal Acara')" />
                            <x-text-input id="event_date" name="event_date" type="date" class="mt-1 block w-full" :value="old('event_date', $event->event_date)" required />
                            <x-input-error :messages="$errors->get('event_date')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="event_time" :value="__('Waktu (opsional)')" />
                            <x-text-input id="event_time" name="event_time" type="time" class="mt-1 block w-full" :value="old('event_time', $event->event_time)" />
                            <x-input-error :messages="$errors->get('event_time')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-5">
                        <x-input-label for="description" :value="__('Deskripsi (opsional)')" />
                        <textarea id="description" name="description" rows="3" class="mt-1 block w-full border-gray-300 dark:border-gray-600 focus:border-skyBlue-500 focus:ring-skyBlue-500 rounded-2xl shadow-soft dark:bg-gray-700 dark:text-gray-200">{{ old('description', $event->description) }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <!-- Recurring -->
                    <div class="mb-5" x-data="{ isRecurring: {{ old('is_recurring', $event->is_recurring) ? 'true' : 'false' }} }">
                        <label class="flex items-center gap-3 cursor-pointer mb-3">
                            <input type="hidden" name="is_recurring" value="0">
                            <input type="checkbox" name="is_recurring" value="1" x-model="isRecurring" class="rounded border-gray-300 dark:border-gray-600 text-skyBlue-500 focus:ring-skyBlue-500 dark:bg-gray-700">
                            <span class="text-sm text-gray-700 dark:text-gray-200">🔁 {{ __('Acara berulang') }}</span>
                        </label>
                        <div x-show="isRecurring" x-transition class="ml-6">
                            <x-input-label for="recurrence_pattern" :value="__('Pola Pengulangan')" />
                            <select id="recurrence_pattern" name="recurrence_pattern" class="mt-1 block w-full border-gray-300 dark:border-gray-600 focus:border-skyBlue-500 focus:ring-skyBlue-500 rounded-2xl shadow-soft dark:bg-gray-700 dark:text-gray-200">
                                <option value="weekly" {{ old('recurrence_pattern', $event->recurrence_pattern) === 'weekly' ? 'selected' : '' }}>Setiap Minggu</option>
                                <option value="monthly" {{ old('recurrence_pattern', $event->recurrence_pattern) === 'monthly' ? 'selected' : '' }}>Setiap Bulan</option>
                                <option value="yearly" {{ old('recurrence_pattern', $event->recurrence_pattern) === 'yearly' ? 'selected' : '' }}>Setiap Tahun</option>
                            </select>
                            <x-input-error :messages="$errors->get('recurrence_pattern')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Reminder -->
                    <div class="mb-6">
                        <x-input-label for="reminder_at" :value="__('Pengingat (opsional)')" />
                        <x-text-input id="reminder_at" name="reminder_at" type="datetime-local" class="mt-1 block w-full" :value="old('reminder_at', $event->reminder_at ? $event->reminder_at->format('Y-m-d\TH:i') : '')" />
                        <x-input-error :messages="$errors->get('reminder_at')" class="mt-2" />
                    </div>

                    <!-- Submit -->
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                        <a href="{{ route('calendar.show', [$child, $event]) }}" class="btn-secondary min-h-[44px]">
                            Batal
                        </a>
                        <button type="submit" class="btn-primary min-h-[44px]">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            {{ __('Perbarui Acara') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
