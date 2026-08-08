@props(['children', 'child', 'routeName'])

@if($children->count() > 1)
    <div class="mb-6 p-4 bg-gradient-to-r from-skyBlue-50 to-lavender-50 rounded-2xl border border-skyBlue-100"
         x-data="{ selectedChildSlug: '{{ $child->slug }}' }"
         x-init="$watch('selectedChildSlug', (val) => { if (val !== '{{ $child->slug }}') window.location.href = '{{ route($routeName, ':slug') }}'.replace(':slug', val); })">
        <div class="flex items-center gap-3">
            <div class="flex-shrink-0">
                <svg class="w-5 h-5 text-skyBlue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <div class="flex-1">
                <label for="child-selector" class="text-sm text-gray-500 block">{{ __('Pindah ke anak lain:') }}</label>
                <select id="child-selector"
                        x-model="selectedChildSlug"
                        class="mt-1 block w-full border-gray-300 focus:border-skyBlue-500 focus:ring-skyBlue-500 rounded-xl shadow-soft text-sm">
                    @foreach($children as $c)
                        <option value="{{ $c->slug }}" {{ $c->slug === $child->slug ? 'selected' : '' }}>
                            {{ $c->gender === 'female' ? '👧' : '👦' }} {{ $c->nickname ?? $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
@endif
