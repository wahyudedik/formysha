<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('diaries.index', $child) }}" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    📝 {{ $diary->title }}
                </h2>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('diaries.edit', [$child, $diary]) }}" class="btn-secondary text-sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    {{ __('Edit') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 p-4 bg-mintGreen-50 border border-mintGreen-200 text-mintGreen-700 rounded-xl" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Diary Card -->
            <div class="bg-white overflow-hidden shadow-soft sm:rounded-3xl">
                <!-- Header -->
                <div class="bg-gradient-to-r from-peach-50 to-warmYellow-50 p-6 sm:p-8">
                    <div class="flex items-start justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800">{{ $diary->title }}</h1>
                            <div class="mt-2 flex flex-wrap items-center gap-3 text-sm text-gray-500">
                                <span>📅 {{ $diary->formatted_date }}</span>
                                @if ($diary->mood)
                                    <span>{{ $diary->mood_label }}</span>
                                @endif
                                @if ($diary->weather)
                                    <span>{{ $diary->weather_label }}</span>
                                @endif
                                @if ($diary->is_private)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-gray-200 text-gray-600 text-xs">🔒 {{ __('Privat') }}</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-mintGreen-100 text-mintGreen-600 text-xs">🌐 {{ __('Publik') }}</span>
                                @endif
                            </div>
                        </div>

                        <!-- Delete Button -->
                        <div x-data="{ showDeleteConfirm: false }">
                            <button @click="showDeleteConfirm = true" class="text-gray-400 hover:text-red-500 transition p-2 rounded-xl hover:bg-red-50">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>

                            <!-- Delete Confirmation Modal -->
                            <div x-show="showDeleteConfirm" x-cloak class="fixed inset-0 z-50 overflow-y-auto" x-transition>
                                <div class="flex items-center justify-center min-h-screen px-4">
                                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="showDeleteConfirm = false"></div>
                                    <div class="relative bg-white rounded-3xl shadow-soft max-w-md w-full p-6 z-10">
                                        <h3 class="text-lg font-bold text-gray-800 mb-2">{{ __('Hapus Catatan?') }}</h3>
                                        <p class="text-gray-500 mb-6">{{ __('Tindakan ini tidak dapat dibatalkan.') }}</p>
                                        <div class="flex items-center gap-3 justify-end">
                                            <button @click="showDeleteConfirm = false" class="btn-secondary">
                                                {{ __('Batal') }}
                                            </button>
                                            <form method="POST" action="{{ route('diaries.destroy', [$child, $diary]) }}" x-on:submit="showDeleteConfirm = false">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-500 text-white font-semibold text-sm rounded-xl hover:bg-red-600 transition">
                                                    {{ __('Hapus') }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-6 sm:p-8">
                    <div class="prose prose-gray max-w-none">
                        {!! nl2br(e($diary->content)) !!}
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 sm:px-8 py-4 border-t border-gray-100 flex items-center justify-between text-xs text-gray-400">
                    <span>{{ __('Ditulis oleh') }} {{ $diary->user->name }}</span>
                    <span>{{ __('Dibuat') }} {{ $diary->created_at->locale('id')->isoFormat('D MMMM YYYY, HH:mm') }}</span>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
