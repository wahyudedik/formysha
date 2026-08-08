<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                🎨 {{ __('Branding') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-6">
            {{-- Sidebar --}}
            @include('admin.partials.sidebar')

            {{-- Main Content --}}
            <div class="flex-1 min-w-0">
                <x-breadcrumb :items="[
                    ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
                    ['label' => 'Branding'],
                ]" />

                <form method="POST" action="{{ route('admin.branding.update') }}" enctype="multipart/form-data" x-data="brandingForm()">
                    @csrf
                    @method('PUT')

                    {{-- Toggle Advanced Settings --}}
                    <div class="mb-6">
                        <button type="button" @click="showAdvanced = !showAdvanced"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                            <span x-text="showAdvanced ? '◀️ Sembunyikan Pengaturan Lanjutan' : '▶️ Pengaturan Lanjutan'"></span>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        {{-- Form Section --}}
                        <div class="space-y-6">
                            {{-- Organization Name --}}
                            <div class="bg-white rounded-2xl shadow-soft p-6 border border-gray-100">
                                <h3 class="font-semibold text-gray-800 mb-4">📋 {{ __('Informasi Organisasi') }}</h3>
                                <div>
                                    <label for="organization_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Organisasi</label>
                                    <input
                                        type="text"
                                        id="organization_name"
                                        name="organization_name"
                                        value="{{ old('organization_name', $branding->organization_name) }}"
                                        class="w-full rounded-xl border-gray-300 focus:border-skyBlue-500 focus:ring-skyBlue-500"
                                        placeholder="Nama organisasi Anda"
                                    >
                                    @error('organization_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Logo & Favicon --}}
                            <div class="bg-white rounded-2xl shadow-soft p-6 border border-gray-100">
                                <h3 class="font-semibold text-gray-800 mb-4">🖼️ {{ __('Logo & Favicon') }}</h3>
                                <div class="space-y-4">
                                    {{-- Logo --}}
                                    <div>
                                        <label for="logo" class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
                                        <input
                                            type="file"
                                            id="logo"
                                            name="logo"
                                            accept="image/*"
                                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-skyBlue-50 file:text-skyBlue-700 hover:file:bg-skyBlue-100"
                                            @change="previewLogo($event)"
                                        >
                                        <p class="mt-1 text-xs text-gray-400">Format: PNG, JPG, SVG. Maks 2MB.</p>
                                        @if ($branding->logo_path)
                                            <div class="mt-2">
                                                <img src="{{ Storage::disk('public')->url($branding->logo_path) }}" alt="Logo" class="h-12 rounded-lg">
                                            </div>
                                        @endif
                                        <div x-show="logoPreview" class="mt-2">
                                            <img :src="logoPreview" alt="Preview" class="h-12 rounded-lg">
                                        </div>
                                        @error('logo')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Favicon --}}
                                    <div>
                                        <label for="favicon" class="block text-sm font-medium text-gray-700 mb-1">Favicon</label>
                                        <input
                                            type="file"
                                            id="favicon"
                                            name="favicon"
                                            accept="image/*"
                                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-lavender-50 file:text-lavender-700 hover:file:bg-lavender-100"
                                            @change="previewFavicon($event)"
                                        >
                                        <p class="mt-1 text-xs text-gray-400">Format: PNG, ICO. Maks 512KB.</p>
                                        @if ($branding->favicon_path)
                                            <div class="mt-2">
                                                <img src="{{ Storage::disk('public')->url($branding->favicon_path) }}" alt="Favicon" class="h-8 rounded">
                                            </div>
                                        @endif
                                        <div x-show="faviconPreview" class="mt-2">
                                            <img :src="faviconPreview" alt="Preview" class="h-8 rounded">
                                        </div>
                                        @error('favicon')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Colors --}}
                            <div class="bg-white rounded-2xl shadow-soft p-6 border border-gray-100">
                                <h3 class="font-semibold text-gray-800 mb-4">🎨 {{ __('Warna') }}</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <div>
                                        <label for="primary_color" class="block text-sm font-medium text-gray-700 mb-1">Warna Primer</label>
                                        <div class="flex items-center gap-2">
                                            <input
                                                type="color"
                                                id="primary_color"
                                                name="primary_color"
                                                value="{{ old('primary_color', $branding->primary_color ?? '#7DD3FC') }}"
                                                class="w-10 h-10 rounded-lg border-0 cursor-pointer"
                                                x-model="primaryColor"
                                            >
                                            <input
                                                type="text"
                                                :value="primaryColor"
                                                class="flex-1 rounded-xl border-gray-300 text-sm"
                                                readonly
                                            >
                                        </div>
                                    </div>
                                    <div>
                                        <label for="secondary_color" class="block text-sm font-medium text-gray-700 mb-1">Warna Sekunder</label>
                                        <div class="flex items-center gap-2">
                                            <input
                                                type="color"
                                                id="secondary_color"
                                                name="secondary_color"
                                                value="{{ old('secondary_color', $branding->secondary_color ?? '#6EE7B7') }}"
                                                class="w-10 h-10 rounded-lg border-0 cursor-pointer"
                                                x-model="secondaryColor"
                                            >
                                            <input
                                                type="text"
                                                :value="secondaryColor"
                                                class="flex-1 rounded-xl border-gray-300 text-sm"
                                                readonly
                                            >
                                        </div>
                                    </div>
                                    <div>
                                        <label for="accent_color" class="block text-sm font-medium text-gray-700 mb-1">Warna Aksen</label>
                                        <div class="flex items-center gap-2">
                                            <input
                                                type="color"
                                                id="accent_color"
                                                name="accent_color"
                                                value="{{ old('accent_color', $branding->accent_color ?? '#FCD34D') }}"
                                                class="w-10 h-10 rounded-lg border-0 cursor-pointer"
                                                x-model="accentColor"
                                            >
                                            <input
                                                type="text"
                                                :value="accentColor"
                                                class="flex-1 rounded-xl border-gray-300 text-sm"
                                                readonly
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Custom CSS --}}
                            <div class="bg-white rounded-2xl shadow-soft p-6 border border-gray-100">
                                <h3 class="font-semibold text-gray-800 mb-4">💻 {{ __('app.branding.custom_css') }}</h3>
                                <textarea
                                    name="custom_css"
                                    rows="6"
                                    class="w-full rounded-xl border-gray-300 font-mono text-sm focus:border-skyBlue-500 focus:ring-skyBlue-500"
                                    placeholder="/* Custom CSS untuk organisasi Anda */"
                                >{{ old('custom_css', $branding->custom_css) }}</textarea>
                                <p class="mt-1 text-xs text-gray-400">{{ __('app.branding.css_hint') }}</p>
                                @error('custom_css')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Submit --}}
                            <div class="flex justify-end">
                                <button type="submit" class="btn-primary">
                                    💾 {{ __('app.actions.save') }}
                                </button>
                            </div>
                        </div>

                        {{-- Advanced Settings Section --}}
                        <div class="space-y-6" x-show="showAdvanced" x-transition>
                            {{-- Login Customization --}}
                            <div class="bg-white rounded-2xl shadow-soft p-6 border border-gray-100">
                                <h3 class="font-semibold text-gray-800 mb-4">🔐 {{ __('app.branding.login_customization') }}</h3>
                                <div class="space-y-4">
                                    <div>
                                        <label for="login_heading" class="block text-sm font-medium text-gray-700 mb-1">Heading Login</label>
                                        <input
                                            type="text"
                                            id="login_heading"
                                            name="login_heading"
                                            value="{{ old('login_heading', $branding->login_heading) }}"
                                            class="w-full rounded-xl border-gray-300 focus:border-skyBlue-500 focus:ring-skyBlue-500"
                                            placeholder="Selamat Datang di ForMysha"
                                        >
                                        @error('login_heading')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="login_subheading" class="block text-sm font-medium text-gray-700 mb-1">Subheading Login</label>
                                        <input
                                            type="text"
                                            id="login_subheading"
                                            name="login_subheading"
                                            value="{{ old('login_subheading', $branding->login_subheading) }}"
                                            class="w-full rounded-xl border-gray-300 focus:border-skyBlue-500 focus:ring-skyBlue-500"
                                            placeholder="Digital Life Book untuk keluarga Anda"
                                        >
                                        @error('login_subheading')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Email Sender --}}
                            <div class="bg-white rounded-2xl shadow-soft p-6 border border-gray-100">
                                <h3 class="font-semibold text-gray-800 mb-4">📧 {{ __('app.branding.email_sender') }}</h3>
                                <div class="space-y-4">
                                    <div>
                                        <label for="email_sender_name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.branding.email_sender_name') }}</label>
                                        <input
                                            type="text"
                                            id="email_sender_name"
                                            name="email_sender_name"
                                            value="{{ old('email_sender_name', $branding->email_sender_name) }}"
                                            class="w-full rounded-xl border-gray-300 focus:border-skyBlue-500 focus:ring-skyBlue-500"
                                            placeholder="Nama Pengirim"
                                        >
                                        @error('email_sender_name')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="email_sender_email" class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.branding.email_sender_email') }}</label>
                                        <input
                                            type="email"
                                            id="email_sender_email"
                                            name="email_sender_email"
                                            value="{{ old('email_sender_email', $branding->email_sender_email) }}"
                                            class="w-full rounded-xl border-gray-300 focus:border-skyBlue-500 focus:ring-skyBlue-500"
                                            placeholder="noreply@example.com"
                                        >
                                        @error('email_sender_email')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Footer Text --}}
                            <div class="bg-white rounded-2xl shadow-soft p-6 border border-gray-100">
                                <h3 class="font-semibold text-gray-800 mb-4">📝 {{ __('app.branding.footer_text') }}</h3>
                                <textarea
                                    name="footer_text"
                                    rows="3"
                                    class="w-full rounded-xl border-gray-300 focus:border-skyBlue-500 focus:ring-skyBlue-500"
                                    placeholder="Teks footer kustom untuk aplikasi Anda"
                                >{{ old('footer_text', $branding->footer_text) }}</textarea>
                                @error('footer_text')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- White Label Toggle --}}
                            <div class="bg-white rounded-2xl shadow-soft p-6 border border-gray-100">
                                <h3 class="font-semibold text-gray-800 mb-4">🏷️ {{ __('app.saas.white_label') }}</h3>
                                <div class="flex items-center gap-3">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="hidden" name="is_white_label_enabled" value="0">
                                        <input
                                            type="checkbox"
                                            name="is_white_label_enabled"
                                            value="1"
                                            class="sr-only peer"
                                            {{ old('is_white_label_enabled', $branding->is_white_label_enabled) ? 'checked' : '' }}
                                        >
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-skyBlue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-skyBlue-600"></div>
                                    </label>
                                    <span class="text-sm text-gray-600">Aktifkan White Label</span>
                                </div>
                            </div>

                            {{-- Submit Advanced --}}
                            <div class="flex justify-end">
                                <button type="submit" class="btn-primary">
                                    💾 {{ __('app.actions.save') }} {{ __('app.branding.advanced_settings') }}
                                </button>
                            </div>
                        </div>

                        {{-- Preview Section --}}
                        <div class="space-y-6">
                            <div class="bg-white rounded-2xl shadow-soft p-6 border border-gray-100 sticky top-24">
                                <h3 class="font-semibold text-gray-800 mb-4">👁️ {{ __('Preview') }}</h3>
                                <div class="rounded-xl border border-gray-200 overflow-hidden">
                                    {{-- Header Preview --}}
                                    <div class="p-4" :style="{ backgroundColor: primaryColor + '20' }">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white text-sm font-bold" :style="{ backgroundColor: primaryColor }">
                                                A
                                            </div>
                                            <div>
                                                <p class="font-bold text-gray-800" x-text="orgName || '{{ $tenant->name }}'"></p>
                                                <p class="text-xs text-gray-500">Digital Life Book</p>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Body Preview --}}
                                    <div class="p-4 bg-gray-50">
                                        <div class="flex gap-2 mb-3">
                                            <div class="h-2 rounded-full flex-1" :style="{ backgroundColor: primaryColor }"></div>
                                            <div class="h-2 rounded-full flex-1" :style="{ backgroundColor: secondaryColor }"></div>
                                            <div class="h-2 rounded-full flex-1" :style="{ backgroundColor: accentColor }"></div>
                                        </div>
                                        <div class="flex gap-2">
                                            <div class="h-8 rounded-lg flex-1" :style="{ backgroundColor: primaryColor + '30' }"></div>
                                            <div class="h-8 rounded-lg flex-1" :style="{ backgroundColor: secondaryColor + '30' }"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function brandingForm() {
            return {
                showAdvanced: false,
                primaryColor: '{{ $branding->primary_color ?? "#7DD3FC" }}',
                secondaryColor: '{{ $branding->secondary_color ?? "#6EE7B7" }}',
                accentColor: '{{ $branding->accent_color ?? "#FCD34D" }}',
                orgName: '{{ $branding->organization_name }}',
                logoPreview: null,
                faviconPreview: null,
                previewLogo(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.logoPreview = URL.createObjectURL(file);
                    }
                },
                previewFavicon(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.faviconPreview = URL.createObjectURL(file);
                    }
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
