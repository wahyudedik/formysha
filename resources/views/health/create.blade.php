<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => $child->name, 'url' => route('children.show', $child)],
            ['label' => 'Kesehatan', 'url' => route('health.index', $child)],
            ['label' => 'Tambah Catatan'],
        ]" />
        <x-page-header title="Tambah Catatan Kesehatan" subtitle="Catat riwayat kesehatan {{ $child->name }}" />
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-child-selector :children="$children" :child="$child" :route-name="'health.create'" />

            {{-- Child Info --}}
            <div class="mb-6 p-4 bg-skyBlue-50 rounded-2xl border border-skyBlue-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-skyBlue-100 flex items-center justify-center text-lg">
                        👶
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">{{ $child->name }}</p>
                        <p class="text-sm text-gray-500">{{ $child->age ?? 'Baru lahir' }}</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('health.store', $child) }}" method="POST" class="space-y-6">
                @csrf

                {{-- Type --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-sm font-semibold text-gray-800 mb-4">Jenis Catatan</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @php
                            $types = [
                                'immunization' => ['label' => 'Imunisasi', 'icon' => '💉'],
                                'illness' => ['label' => 'Penyakit', 'icon' => '🤒'],
                                'medication' => ['label' => 'Obat', 'icon' => '💊'],
                                'allergy' => ['label' => 'Alergi', 'icon' => '⚠️'],
                                'checkup' => ['label' => 'Pemeriksaan', 'icon' => '🩺'],
                                'other' => ['label' => 'Lainnya', 'icon' => '📋'],
                            ];
                        @endphp
                        @foreach ($types as $typeKey => $typeInfo)
                            <label class="flex items-center gap-2 p-3 rounded-xl border-2 cursor-pointer transition {{ old('type') === $typeKey ? 'border-skyBlue-500 bg-skyBlue-50' : 'border-gray-200 hover:border-gray-300' }}">
                                <input type="radio" name="type" value="{{ $typeKey }}" {{ old('type') === $typeKey ? 'checked' : '' }} class="hidden">
                                <span class="text-lg">{{ $typeInfo['icon'] }}</span>
                                <span class="text-sm font-medium text-gray-700">{{ $typeInfo['label'] }}</span>
                            </label>
                        @endforeach
                    </div>
                    <x-input-error :messages="$errors->get('type')" class="mt-2" />
                </div>

                {{-- Name & Date --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
                    <h3 class="text-sm font-semibold text-gray-800">Detail</h3>

                    <div>
                        <x-input-label for="name" value="Nama *" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ old('name') }}" placeholder="Contoh: BCG, Demam, Parasetamol" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="date" value="Tanggal *" />
                        <x-text-input id="date" name="date" type="date" class="mt-1 block w-full" value="{{ old('date', date('Y-m-d')) }}" required />
                        <x-input-error :messages="$errors->get('date')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="description" value="Deskripsi" />
                        <textarea id="description" name="description" rows="3" class="mt-1 block w-full border-gray-300 rounded-xl focus:border-skyBlue-500 focus:ring-skyBlue-500 shadow-sm text-sm" placeholder="Deskripsi singkat...">{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>
                </div>

                {{-- Doctor & Hospital --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
                    <h3 class="text-sm font-semibold text-gray-800">Dokter & Fasilitas</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="doctor" value="Dokter" />
                            <x-text-input id="doctor" name="doctor" type="text" class="mt-1 block w-full" value="{{ old('doctor') }}" placeholder="Nama dokter" />
                            <x-input-error :messages="$errors->get('doctor')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="hospital" value="Rumah Sakit / Klinik" />
                            <x-text-input id="hospital" name="hospital" type="text" class="mt-1 block w-full" value="{{ old('hospital') }}" placeholder="Nama rumah sakit atau klinik" />
                            <x-input-error :messages="$errors->get('hospital')" class="mt-2" />
                        </div>
                    </div>
                </div>

                {{-- Notes & Next Date --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
                    <h3 class="text-sm font-semibold text-gray-800">Catatan Tambahan</h3>

                    <div>
                        <x-input-label for="notes" value="Catatan" />
                        <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-xl focus:border-skyBlue-500 focus:ring-skyBlue-500 shadow-sm text-sm" placeholder="Catatan tambahan...">{{ old('notes') }}</textarea>
                        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="next_date" value="Jadwal Berikutnya (Opsional)" />
                        <x-text-input id="next_date" name="next_date" type="date" class="mt-1 block w-full" value="{{ old('next_date') }}" />
                        <p class="text-xs text-gray-400 mt-1">Untuk imunisasi atau pemeriksaan rutin berikutnya</p>
                        <x-input-error :messages="$errors->get('next_date')" class="mt-2" />
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('health.index', $child) }}" class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition">
                        Batal
                    </a>
                    <x-primary-button type="submit">
                        💾 Simpan Catatan
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
