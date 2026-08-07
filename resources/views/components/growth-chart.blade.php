@props(['growths', 'whoWeight' => null, 'whoHeight' => null, 'childGender' => null])

@php
    $data = $growths->map(fn ($g) => [
        'date' => $g->measured_at->format('d M Y'),
        'weight' => $g->weight_kg ? (float) $g->weight_kg : null,
        'height' => $g->height_cm ? (float) $g->height_cm : null,
    ])->toArray();

    $weights = collect($data)->pluck('weight')->filter()->values();
    $heights = collect($data)->pluck('height')->filter()->values();

    $hasWho = $whoWeight !== null && $whoHeight !== null;

    // Calculate Y-axis ranges including WHO data if available
    $minWeight = $weights->min() ?? 0;
    $maxWeight = $weights->max() ?? 10;
    $minHeight = $heights->min() ?? 0;
    $maxHeight = $heights->max() ?? 100;

    if ($hasWho) {
        $allWeightWho = array_merge($whoWeight['median'], $whoWeight['minus2sd'], $whoWeight['plus2sd']);
        $allHeightWho = array_merge($whoHeight['median'], $whoHeight['minus2sd'], $whoHeight['plus2sd']);
        $minWeight = min($minWeight, min($allWeightWho));
        $maxWeight = max($maxWeight, max($allWeightWho));
        $minHeight = min($minHeight, min($allHeightWho));
        $maxHeight = max($maxHeight, max($allHeightWho));
    }

    // Add padding to ranges
    $weightRange = max($maxWeight - $minWeight, 1);
    $heightRange = max($maxHeight - $minHeight, 10);
    $minWeight = max(0, $minWeight - $weightRange * 0.1);
    $maxWeight = $maxWeight + $weightRange * 0.1;
    $minHeight = max(0, $minHeight - $heightRange * 0.1);
    $maxHeight = $maxHeight + $heightRange * 0.1;

    // Helper: map WHO percentile month keys to X positions aligned with data points
    // WHO data uses months (0,3,6,9,12,...), we map them proportionally across the chart width
    $chartWidth = max(count($data) * 80, 400);
    $plotLeft = 60;
    $plotRight = $chartWidth - 20;
    $plotWidth = $plotRight - $plotLeft;
    $maxMonths = 60;

    function mapWhoX(float $month, float $plotLeft, float $plotWidth, float $maxMonths): float {
        return $plotLeft + ($month / $maxMonths) * $plotWidth;
    }

    function mapWeightY(float $value, float $minWeight, float $maxWeight): float {
        $normalizedY = ($value - $minWeight) / ($maxWeight - $minWeight);
        return 220 - ($normalizedY * 200);
    }

    function mapHeightY(float $value, float $minHeight, float $maxHeight): float {
        $normalizedY = ($value - $minHeight) / ($maxHeight - $minHeight);
        return 220 - ($normalizedY * 200);
    }

    function buildWhoLine(array $whoData, string $type, float $plotLeft, float $plotWidth, float $maxMonths, $minVal, $maxVal): string {
        $points = [];
        foreach ($whoData[$type] as $month => $value) {
            if ($value !== null) {
                $x = mapWhoX((float) $month, $plotLeft, $plotWidth, $maxMonths);
                if ($type === 'weight' || str_contains($type, 'weight')) {
                    $y = mapWeightY($value, $minVal, $maxVal);
                } else {
                    $y = mapHeightY($value, $minVal, $maxVal);
                }
                $points[] = "{$x},{$y}";
            }
        }
        return implode(' ', $points);
    }
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
        @if ($weights->isEmpty() && (!$hasWho || empty($whoWeight['median'])))
            <div class="text-center py-8 text-gray-400">
                <p class="text-sm">{{ __('Belum ada data berat badan.') }}</p>
            </div>
        @else
            <div class="relative overflow-x-auto">
                <svg viewBox="0 0 {{ $chartWidth }} 260" class="w-full min-w-[400px]" xmlns="http://www.w3.org/2000/svg">
                    <!-- Grid lines -->
                    @for ($i = 0; $i <= 4; $i++)
                        @php $y = 20 + ($i * 50); @endphp
                        <line x1="{{ $plotLeft }}" y1="{{ $y }}" x2="100%" y2="{{ $y }}" stroke="#f3f4f6" stroke-width="1"/>
                        <text x="{{ $plotLeft - 5 }}" y="{{ $y + 4 }}" text-anchor="end" class="text-[10px] fill-gray-400">
                            {{ number_format($maxWeight - ($i * ($maxWeight - $minWeight) / 4), 1) }}
                        </text>
                    @endfor

                    @if ($hasWho)
                        <!-- WHO +2SD line (upper bound) -->
                        @php $whoUpperPts = []; @endphp
                        @foreach ($whoWeight['plus2sd'] as $month => $val)
                            @if ($val !== null)
                                @php
                                    $wx = mapWhoX((float) $month, $plotLeft, $plotWidth, $maxMonths);
                                    $wy = mapWeightY($val, $minWeight, $maxWeight);
                                    $whoUpperPts[] = "{$wx},{$wy}";
                                @endphp
                            @endif
                        @endforeach
                        @if (count($whoUpperPts) >= 2)
                            <polyline points="{{ implode(' ', $whoUpperPts) }}" fill="none" stroke="#fca5a5" stroke-width="1.5" stroke-dasharray="6,3" opacity="0.7"/>
                        @endif

                        <!-- WHO median line -->
                        @php $whoMedianPts = []; @endphp
                        @foreach ($whoWeight['median'] as $month => $val)
                            @if ($val !== null)
                                @php
                                    $wx = mapWhoX((float) $month, $plotLeft, $plotWidth, $maxMonths);
                                    $wy = mapWeightY($val, $minWeight, $maxWeight);
                                    $whoMedianPts[] = "{$wx},{$wy}";
                                @endphp
                            @endif
                        @endforeach
                        @if (count($whoMedianPts) >= 2)
                            <polyline points="{{ implode(' ', $whoMedianPts) }}" fill="none" stroke="#86efac" stroke-width="1.5" stroke-dasharray="6,3" opacity="0.7"/>
                        @endif

                        <!-- WHO -2SD line (lower bound) -->
                        @php $whoLowerPts = []; @endphp
                        @foreach ($whoWeight['minus2sd'] as $month => $val)
                            @if ($val !== null)
                                @php
                                    $wx = mapWhoX((float) $month, $plotLeft, $plotWidth, $maxMonths);
                                    $wy = mapWeightY($val, $minWeight, $maxWeight);
                                    $whoLowerPts[] = "{$wx},{$wy}";
                                @endphp
                            @endif
                        @endforeach
                        @if (count($whoLowerPts) >= 2)
                            <polyline points="{{ implode(' ', $whoLowerPts) }}" fill="none" stroke="#fca5a5" stroke-width="1.5" stroke-dasharray="6,3" opacity="0.7"/>
                        @endif

                        <!-- WHO Legend -->
                        <g transform="translate({{ $plotLeft + 10 }}, 12)">
                            <line x1="0" y1="0" x2="20" y2="0" stroke="#86efac" stroke-width="1.5" stroke-dasharray="6,3"/>
                            <text x="24" y="4" class="text-[9px] fill-gray-400">{{ __('Median WHO') }}</text>
                            <line x1="100" y1="0" x2="120" y2="0" stroke="#fca5a5" stroke-width="1.5" stroke-dasharray="6,3"/>
                            <text x="124" y="4" class="text-[9px] fill-gray-400">{{ __('±2 SD') }}</text>
                        </g>
                    @endif

                    <!-- Data points and line -->
                    @php
                        $points = [];
                        foreach ($data as $index => $item) {
                            if ($item['weight'] !== null) {
                                $x = $plotLeft + 20 + ($index * 80);
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
                                $x = $plotLeft + 20 + ($index * 80);
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
        @if ($heights->isEmpty() && (!$hasWho || empty($whoHeight['median'])))
            <div class="text-center py-8 text-gray-400">
                <p class="text-sm">{{ __('Belum ada data tinggi badan.') }}</p>
            </div>
        @else
            <div class="relative overflow-x-auto">
                <svg viewBox="0 0 {{ $chartWidth }} 260" class="w-full min-w-[400px]" xmlns="http://www.w3.org/2000/svg">
                    <!-- Grid lines -->
                    @for ($i = 0; $i <= 4; $i++)
                        @php $y = 20 + ($i * 50); @endphp
                        <line x1="{{ $plotLeft }}" y1="{{ $y }}" x2="100%" y2="{{ $y }}" stroke="#f3f4f6" stroke-width="1"/>
                        <text x="{{ $plotLeft - 5 }}" y="{{ $y + 4 }}" text-anchor="end" class="text-[10px] fill-gray-400">
                            {{ number_format($maxHeight - ($i * ($maxHeight - $minHeight) / 4), 0) }}
                        </text>
                    @endfor

                    @if ($hasWho)
                        <!-- WHO +2SD line (upper bound) -->
                        @php $whoUpperPts = []; @endphp
                        @foreach ($whoHeight['plus2sd'] as $month => $val)
                            @if ($val !== null)
                                @php
                                    $wx = mapWhoX((float) $month, $plotLeft, $plotWidth, $maxMonths);
                                    $wy = mapHeightY($val, $minHeight, $maxHeight);
                                    $whoUpperPts[] = "{$wx},{$wy}";
                                @endphp
                            @endif
                        @endforeach
                        @if (count($whoUpperPts) >= 2)
                            <polyline points="{{ implode(' ', $whoUpperPts) }}" fill="none" stroke="#fca5a5" stroke-width="1.5" stroke-dasharray="6,3" opacity="0.7"/>
                        @endif

                        <!-- WHO median line -->
                        @php $whoMedianPts = []; @endphp
                        @foreach ($whoHeight['median'] as $month => $val)
                            @if ($val !== null)
                                @php
                                    $wx = mapWhoX((float) $month, $plotLeft, $plotWidth, $maxMonths);
                                    $wy = mapHeightY($val, $minHeight, $maxHeight);
                                    $whoMedianPts[] = "{$wx},{$wy}";
                                @endphp
                            @endif
                        @endforeach
                        @if (count($whoMedianPts) >= 2)
                            <polyline points="{{ implode(' ', $whoMedianPts) }}" fill="none" stroke="#86efac" stroke-width="1.5" stroke-dasharray="6,3" opacity="0.7"/>
                        @endif

                        <!-- WHO -2SD line (lower bound) -->
                        @php $whoLowerPts = []; @endphp
                        @foreach ($whoHeight['minus2sd'] as $month => $val)
                            @if ($val !== null)
                                @php
                                    $wx = mapWhoX((float) $month, $plotLeft, $plotWidth, $maxMonths);
                                    $wy = mapHeightY($val, $minHeight, $maxHeight);
                                    $whoLowerPts[] = "{$wx},{$wy}";
                                @endphp
                            @endif
                        @endforeach
                        @if (count($whoLowerPts) >= 2)
                            <polyline points="{{ implode(' ', $whoLowerPts) }}" fill="none" stroke="#fca5a5" stroke-width="1.5" stroke-dasharray="6,3" opacity="0.7"/>
                        @endif

                        <!-- WHO Legend -->
                        <g transform="translate({{ $plotLeft + 10 }}, 12)">
                            <line x1="0" y1="0" x2="20" y2="0" stroke="#86efac" stroke-width="1.5" stroke-dasharray="6,3"/>
                            <text x="24" y="4" class="text-[9px] fill-gray-400">{{ __('Median WHO') }}</text>
                            <line x1="100" y1="0" x2="120" y2="0" stroke="#fca5a5" stroke-width="1.5" stroke-dasharray="6,3"/>
                            <text x="124" y="4" class="text-[9px] fill-gray-400">{{ __('±2 SD') }}</text>
                        </g>
                    @endif

                    <!-- Data points and line -->
                    @php
                        $points = [];
                        foreach ($data as $index => $item) {
                            if ($item['height'] !== null) {
                                $x = $plotLeft + 20 + ($index * 80);
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
                                $x = $plotLeft + 20 + ($index * 80);
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
