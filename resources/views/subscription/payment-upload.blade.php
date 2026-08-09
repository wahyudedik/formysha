<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            💳 {{ __('Upload Bukti Pembayaran') }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-breadcrumb :items="[
            ['label' => 'Langganan', 'url' => route('subscription.current')],
            ['label' => 'Upload Pembayaran'],
        ]" />

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Subscription Info --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft overflow-hidden">
                <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-100">📋 {{ __('Detail Langganan') }}</h3>
                </div>
                <div class="p-4 sm:p-6 space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Paket</span>
                        <span class="text-sm font-bold text-gray-800 dark:text-gray-100">{{ $subscription->plan->name ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Status</span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-warmYellow-100 dark:bg-warmYellow-950/30 text-warmYellow-600 dark:text-warmYellow-400">
                            Menunggu Pembayaran
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Jumlah</span>
                        <span class="text-lg font-bold text-gray-800 dark:text-gray-100">{{ $subscription->plan->getPriceMonthlyFormatted() }}/bulan</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Dibuat</span>
                        <span class="text-sm text-gray-600 dark:text-gray-300">{{ $subscription->created_at->locale('id')->isoFormat('D MMMM YYYY') }}</span>
                    </div>
                </div>
            </div>

            {{-- Bank Accounts --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-soft overflow-hidden">
                <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-100">🏦 {{ __('Rekening Tujuan') }}</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Transfer ke salah satu rekening berikut:</p>
                </div>
                <div class="p-4 sm:p-6 space-y-4">
                    @foreach (config('saas.banks', []) as $bankName => $bankInfo)
                        <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-700 border border-gray-100 dark:border-gray-600">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-bold text-sm text-gray-800 dark:text-gray-100">{{ $bankName }}</span>
                                <button type="button" class="text-xs text-skyBlue-600 hover:text-skyBlue-700" x-data x-on:click="navigator.clipboard.writeText('{{ $bankInfo['account'] }}'); $el.textContent = 'Tersalin!'; setTimeout(() => $el.textContent = 'Salin', 2000)">
                                    📋 Salin
                                </button>
                            </div>
                            <p class="text-sm font-mono text-gray-700 dark:text-gray-200">{{ $bankInfo['account'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">a/n {{ $bankInfo['holder'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Upload Form --}}
        <div class="mt-6 bg-white dark:bg-gray-800 rounded-2xl shadow-soft overflow-hidden">
            <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-gray-700">
                <h3 class="font-semibold text-gray-800 dark:text-gray-100">📤 {{ __('Upload Bukti Transfer') }}</h3>
            </div>

            <form method="POST" action="{{ route('subscription.payment.store') }}" enctype="multipart/form-data" class="p-4 sm:p-6" x-data="{ loading: false }" @submit="loading = true">
                @csrf

                <input type="hidden" name="subscription_id" value="{{ $subscription->id }}">

                <div class="max-w-lg space-y-5">
                    {{-- Bank Name --}}
                    <div>
                        <x-input-label for="bank_name" :value="__('Bank yang Digunakan')" />
                        <select id="bank_name" name="bank_name" class="mt-1 block w-full border-gray-300 rounded-xl focus:border-softPink-300 focus:ring-softPink-200 text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200" required>
                            <option value="">Pilih bank...</option>
                            @foreach (config('saas.banks', []) as $bankName => $bankInfo)
                                <option value="{{ $bankName }}" {{ old('bank_name') === $bankName ? 'selected' : '' }}>
                                    {{ $bankName }} — {{ $bankInfo['account'] }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('bank_name')" class="mt-1" />
                    </div>

                    {{-- Amount --}}
                    <div>
                        <x-input-label for="amount" :value="__('Jumlah Transfer (Rp)')" />
                        <x-text-input id="amount" name="amount" type="number" class="mt-1 block w-full input-focus" :value="old('amount', $subscription->plan->price_monthly)" min="1" required />
                        <x-input-error :messages="$errors->get('amount')" class="mt-1" />
                    </div>

                    {{-- Proof --}}
                    <div>
                        <x-input-label for="proof" :value="__('Bukti Transfer (Gambar)')" />
                        <div class="mt-2" x-data="{ fileName: '' }">
                            <label for="proof" class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-2xl cursor-pointer hover:border-softPink-300 hover:bg-softPink-50/30 dark:hover:bg-softPink-950/20 transition">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6" x-show="!fileName">
                                    <svg class="w-10 h-10 mb-3 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <p class="text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Klik untuk upload</span></p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500">PNG, JPG, JPEG (Maks. 5MB)</p>
                                </div>
                                <div class="flex flex-col items-center justify-center pt-5 pb-6" x-show="fileName">
                                    <svg class="w-10 h-10 mb-3 text-mintGreen-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <p class="text-sm text-gray-600 dark:text-gray-300 font-medium" x-text="fileName"></p>
                                </div>
                                <input id="proof" name="proof" type="file" class="hidden" accept="image/*" required x-on:change="fileName = $event.target.files[0].name">
                            </label>
                        </div>
                        <x-input-error :messages="$errors->get('proof')" class="mt-1" />
                    </div>

                    {{-- Notes --}}
                    <div>
                        <x-input-label for="notes" :value="__('Catatan (opsional)')" />
                        <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-xl focus:border-softPink-300 focus:ring-softPink-200 text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200" placeholder="Contoh: Transfer dari BCA...">{{ old('notes') }}</textarea>
                        <x-input-error :messages="$errors->get('notes')" class="mt-1" />
                    </div>
                </div>

                <div class="mt-6 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    <button type="submit" :disabled="loading" class="btn-primary text-sm min-h-[44px] disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg x-show="loading" class="w-4 h-4 mr-1 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span x-text="loading ? '{{ __('Mengirim...') }}' : '📤 {{ __('Kirim Bukti Transfer') }}'"></span>
                    </button>
                    <a href="{{ route('subscription.current') }}" class="btn-secondary text-sm min-h-[44px] inline-flex items-center justify-center">
                        {{ __('Batal') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
