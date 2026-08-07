@props(['growths'])

@php
    $data = $growths->map(fn ($g) => [
        'date' => $g->measured_at->format('d M Y'),
        'weight' => $g->weight_kg ? (float) $g->weight_kg : null,
        'height' => $g->height_cm ? (float) $g->height_cm : null,
    ])->toArray();

    $weights = collect($data)->pluck('weight')->filter()->values();
    $heights = collect($data)->pluck('height')->filter()->values();

    $minWeight = $weights->min() ?? 0;
    $maxWeight = $weights->max() ?? 10;
    $minHeight = $heights->min() ?? 0;
    $maxHeight = $heights->max() ?? 100;

    // Add padding to ranges
    $weightRange = max($maxWeight - $minWeight, 1);
    $heightRange = max($maxHeight - $minHeight, 10);
    $minWeight = max(0, $minWeight - $weightRange * 0.1);
    $maxWeight = $maxWeight + $weightRange * 0.1;
    $minHeight = max(0, $minHeight - $heightRange * 0.1);
    $maxHeight = $maxHeight + $heightRange * 0.1;
@endphp

<div x-data="{ tab: 'weight' }" class="w-full">
    <!-- Tab Buttons -->
    <div class="flex gap-2 mb-6">
        <button @click="tab = 'weight'" :class="tab === 'weight' ? 'bg-softPink-100 text-softPink-700 border-softPink-200' : 'bg-gray-50 text-gray-500 border-gray-200 hover:bg-gray-100'" class="px-4 py-2 rounded-xl text-sm font-medium border transition-all duration-200">
            ⚖️ {{ __('Berat Badan') }}
        </button>
        <button @click="tab = 'height'" :class="tab === 'height' ? 'bg-skyBlue-100 text-skyBlue-700 border-skyBlue-200' : 'bg-gray-50 text-gray-500 border-gray-200 hover:bg-gray-100'" class="px-4 py-2 rounded-xl text-sm font-medium border transition-all duration-200">
            📐 {{ __('Tinggi Badan') }}
        </button>
    </div>

    <!-- Weight Chart -->
    <div x-show="tab === 'weight'" x-transition>
        @if ($weights->isEmpty())
            <div class="text-center py-8 text-gray-400">
                <p class="text-sm">{{ __('Belum ada data berat badan.') }}</p>
            </div>
        @else
            <div class="relative overflow-x-auto">
                <svg viewBox="0 0 {{ max(count($data) * 80, 400) }} 250" class="w-full min-w-[400px]" xmlns="http://www.w3.org/2000/svg">
                    <!-- Grid lines -->
                    @for ($i = 0; $i <= 4; $i++)
                        @php $y = 20 + ($i * 50); @endphp
                        <line x1="40" y1="{{ $y }}" x2="100%" y2="{{ $y }}" stroke="#f3f4f6" stroke-width="1"/>
                        <text x="35" y="{{ $y + 4 }}" text-anchor="end" class="text-[10px] fill-gray-400">
                            {{ number_format($maxWeight - ($i * ($maxWeight - $minWeight) / 4), 1) }}
                        </text>
                    @endfor

                    <!-- Data points and line -->
                    @php
                        $points = [];
                        foreach ($data as $index => $item) {
                            if ($item['weight'] !== null) {
                                $x = 60 + ($index * 80);
                                $normalizedY = ($item['weight'] - $minWeight) / ($maxWeight - $minWeight);
                                $y = 220 - ($normalizedY * 200);
                                $points[] = "{$x},{$y}";
                            }
                        }
                    @endphp

                    @if (count($points) >= 2)
                        <polyline points="{{ implode(' ', $points) }}" fill="none" stroke="#f9a8d4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    @endif

                    @foreach ($data as $index => $item)
                        @if ($item['weight'] !== null)
                            @php
                                $x = 60 + ($index * 80);
                                $normalizedY = ($item['weight'] - $minWeight) / ($maxWeight - $minWeight);
                                $y = 220 - ($normalizedY * 200);
                            @endphp
                            <circle cx="{{ $x }}" cy="{{ $y }}" r="5" fill="#ec4899" stroke="white" stroke-width="2"/>
                            <text x="{{ $x }}" y="{{ $y - 12 }}" text-anchor="middle" class="text-[10px] fill-gray-600 font-medium">
                                {{ number_format($item['weight'], 1) }}
                            </text>
                            <text x="{{ $x }}" y="240" text-anchor="middle" class="text-[9px] fill-gray-400">
                                {{ \Carbon\Carbon::parse($item['date'])->format('d M') }}
                            </text>
                        @endif
                    @endforeach
                </svg>
            </div>
        @endif
    </div>

    <!-- Height Chart -->
    <div x-show="tab === 'height'" x-transition>
        @if ($heights->isEmpty())
            <div class="text-center py-8 text-gray-400">
                <p class="text-sm">{{ __('Belum ada data tinggi badan.') }}</p>
            </div>
        @else
            <div class="relative overflow-x-auto">
                <svg viewBox="0 0 {{ max(count($data) * 80, 400) }} 250" class="w-full min-w-[400px]" xmlns="http://www.w3.org/2000/svg">
                    <!-- Grid lines -->
                    @for ($i = 0; $i <= 4; $i++)
                        @php $y = 20 + ($i * 50); @endphp
                        <line x1="40" y1="{{ $y }}" x2="100%" y2="{{ $y }}" stroke="#f3f4f6" stroke-width="1"/>
                        <text x="35" y="{{ $y + 4 }}" text-anchor="end" class="text-[10px] fill-gray-400">
                            {{ number_format($maxHeight - ($i * ($maxHeight - $minHeight) / 4), 0) }}
                        </text>
                    @endfor

                    <!-- Data points and line -->
                    @php
                        $points = [];
                        foreach ($data as $index => $item) {
                            if ($item['height'] !== null) {
                                $x = 60 + ($index * 80);
                                $normalizedY = ($item['height'] - $minHeight) / ($maxHeight - $minHeight);
                                $y = 220 - ($normalizedY * 200);
                                $points[] = "{$x},{$y}";
                            }
                        }
                    @endphp

                    @if (count($points) >= 2)
                        <polyline points="{{ implode(' ', $points) }}" fill="none" stroke="#93c5fd" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    @endif

                    @foreach ($data as $index => $item)
                        @if ($item['height'] !== null)
                            @php
                                $x = 60 + ($index * 80);
                                $normalizedY = ($item['height'] - $minHeight) / ($maxHeight - $minHeight);
                                $y = 220 - ($normalizedY * 200);
                            @endphp
                            <circle cx="{{ $x }}" cy="{{ $y }}" r="5" fill="#3b82f6" stroke="white" stroke-width="2"/>
                            <text x="{{ $x }}" y="{{ $y - 12 }}" text-anchor="middle" class="text-[10px] fill-gray-600 font-medium">
                                {{ number_format($item['height'], 1) }}
                            </text>
                            <text x="{{ $x }}" y="240" text-anchor="middle" class="text-[9px] fill-gray-400">
                                {{ \Carbon\Carbon::parse($item['date'])->format('d M') }}
                            </text>
                        @endif
                    @endforeach
                </svg>
            </div>
        @endif
    </div>
</div>
