@props([
    'name',
    'label',
    'options' => [],
    'placeholder' => 'Ketik untuk mencari...',
    'valueKey' => 'value',
    'labelKey' => 'label',
    'sublabelKey' => null,
    'selected' => null,
    'required' => false,
    'error' => null,
])

@php
    $selectedValue = $selected ?? old($name);
    $componentId = $name . '_' . uniqid();
@endphp

<div x-data="{
    open: false,
    search: '',
    selected: '{{ $selectedValue }}',
    selectedLabel: '',
    highlightIndex: -1,
    get filteredOptions() {
        const query = this.search.toLowerCase().trim();
        const options = {{ Js::from($options) }};
        if (!query) return options;
        return options.filter(opt =>
            (opt.label || '').toLowerCase().includes(query) ||
            (opt.sublabel || '').toLowerCase().includes(query)
        );
    },
    selectOption(value, label) {
        this.selected = value;
        this.selectedLabel = label;
        this.search = '';
        this.open = false;
        this.highlightIndex = -1;
        this.$refs.hiddenInput.value = value;
    },
    clearSelection() {
        this.selected = '';
        this.selectedLabel = '';
        this.search = '';
        this.$refs.hiddenInput.value = '';
    },
    handleKeydown(e) {
        const options = this.filteredOptions;
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            this.highlightIndex = Math.min(this.highlightIndex + 1, options.length - 1);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            this.highlightIndex = Math.max(this.highlightIndex - 1, -1);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (this.highlightIndex >= 0 && options[this.highlightIndex]) {
                this.selectOption(options[this.highlightIndex].value, options[this.highlightIndex].label);
            }
        } else if (e.key === 'Escape') {
            this.open = false;
            this.highlightIndex = -1;
        }
    },
    init() {
        const options = {{ Js::from($options) }};
        const selectedOpt = options.find(o => o.value === '{{ $selectedValue }}');
        if (selectedOpt) {
            this.selectedLabel = selectedOpt.label;
        }
    }
}" x-on:click.outside="open = false" class="relative">
    <input type="hidden" name="{{ $name }}" x-ref="hiddenInput" value="{{ $selectedValue }}" {{ $required ? 'required' : '' }} />

    @if($label)
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ $label }} {{ $required ? '*' : '' }}</label>
    @endif

    {{-- Display button when not searching --}}
    <div class="relative">
        <button type="button"
            x-on:click="open = !open"
            x-show="!open"
            class="mt-1 w-full flex items-center justify-between border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-xl shadow-sm px-4 py-3 text-left text-sm transition focus:border-softPink-300 focus:ring-2 focus:ring-softPink-200 min-h-[44px]"
        >
            <span x-text="selectedLabel || '{{ __('Pilih...') }}'" :class="selected ? 'text-gray-900 dark:text-gray-100' : 'text-gray-400 dark:text-gray-500'"></span>
            <svg class="w-5 h-5 text-gray-400 shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        {{-- Search input when open --}}
        <div x-show="open" x-cloak class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input type="text"
                x-model="search"
                x-on:keydown="handleKeydown($event)"
                x-on:focus="open = true"
                class="mt-1 w-full border border-softPink-300 dark:border-softPink-400 dark:bg-gray-700 dark:text-gray-200 rounded-xl shadow-sm pl-10 pr-4 py-3 text-sm focus:border-softPink-300 focus:ring-2 focus:ring-softPink-200 transition min-h-[44px]"
                placeholder="{{ $placeholder }}"
                autofocus
            />
        </div>
    </div>

    {{-- Dropdown list --}}
    <div x-show="open" x-cloak
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-50 mt-1 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-xl shadow-lg max-h-[250px] overflow-y-auto"
    >
        {{-- No results --}}
        <div x-show="filteredOptions.length === 0" class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 text-center">
            {{ __('Tidak ditemukan') }}
        </div>

        {{-- Options --}}
        <template x-for="(option, index) in filteredOptions" :key="option.value">
            <button type="button"
                x-on:click="selectOption(option.value, option.label)"
                x-on:mouseenter="highlightIndex = index"
                class="w-full text-left px-4 py-3 text-sm flex items-center justify-between transition min-h-[44px]"
                :class="{
                    'bg-softPink-50 dark:bg-softPink-900/30': index === highlightIndex,
                    'bg-softPink-100 dark:bg-softPink-800/40': selected === option.value,
                    'hover:bg-gray-50 dark:hover:bg-gray-700': index !== highlightIndex && selected !== option.value
                }"
            >
                <div class="min-w-0">
                    <div class="text-gray-900 dark:text-gray-100 truncate" x-text="option.label"></div>
                    <div x-show="option.sublabel" class="text-xs text-gray-500 dark:text-gray-400 truncate" x-text="option.sublabel"></div>
                </div>
                <svg x-show="selected === option.value" class="w-5 h-5 text-softPink-500 shrink-0 ml-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
            </button>
        </template>
    </div>

    {{-- Clear button --}}
    <button type="button" x-show="selected && !open" x-on:click="clearSelection()"
        class="absolute right-10 top-1/2 -translate-y-1/2 mt-0.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition p-1"
        title="{{ __('Hapus pilihan') }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>

    @if($error)
        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $error }}</p>
    @endif
</div>
