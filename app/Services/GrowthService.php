<?php

namespace App\Services;

use App\Models\Child;
use App\Models\Growth;
use Carbon\Carbon;

class GrowthService
{
    /**
     * WHO Growth Standards — Weight-for-age (boys/girls) in kg.
     * Keys are age in months, values are [median, -2SD, +2SD].
     *
     * @var array<string, array{float, float, float}>
     */
    private const WHO_WEIGHT_BOYS = [
        0 => [3.3, 2.5, 4.4],
        3 => [6.4, 4.9, 8.2],
        6 => [7.9, 6.1, 10.3],
        9 => [8.9, 6.9, 11.4],
        12 => [9.6, 7.4, 12.3],
        15 => [10.3, 8.0, 13.1],
        18 => [10.9, 8.5, 13.9],
        24 => [12.2, 9.5, 15.5],
        36 => [14.3, 11.0, 18.5],
        48 => [16.3, 12.5, 21.0],
        60 => [18.3, 14.0, 23.8],
    ];

    private const WHO_WEIGHT_GIRLS = [
        0 => [3.2, 2.4, 4.2],
        3 => [5.8, 4.5, 7.6],
        6 => [7.3, 5.7, 9.6],
        9 => [8.2, 6.4, 10.6],
        12 => [8.9, 6.9, 11.5],
        15 => [9.6, 7.4, 12.3],
        18 => [10.2, 7.9, 13.2],
        24 => [11.5, 8.9, 14.9],
        36 => [13.9, 10.7, 18.1],
        48 => [16.1, 12.4, 20.9],
        60 => [18.2, 13.9, 23.6],
    ];

    /**
     * WHO Growth Standards — Height-for-age (boys/girls) in cm.
     *
     * @var array<string, array{float, float, float}>
     */
    private const WHO_HEIGHT_BOYS = [
        0 => [49.1, 45.4, 52.9],
        3 => [61.4, 57.6, 65.3],
        6 => [67.6, 63.3, 71.9],
        9 => [72.0, 67.5, 76.4],
        12 => [75.7, 71.0, 80.5],
        15 => [78.5, 73.6, 83.5],
        18 => [81.0, 76.0, 86.1],
        24 => [85.7, 80.5, 91.1],
        36 => [95.1, 89.3, 101.2],
        48 => [102.9, 96.6, 109.4],
        60 => [109.9, 103.2, 116.8],
    ];

    private const WHO_HEIGHT_GIRLS = [
        0 => [48.6, 45.0, 52.3],
        3 => [59.8, 56.1, 63.7],
        6 => [65.7, 61.3, 70.3],
        9 => [70.1, 65.5, 74.9],
        12 => [74.0, 68.9, 79.2],
        15 => [76.9, 71.6, 82.4],
        18 => [79.4, 73.8, 85.0],
        24 => [84.3, 78.4, 90.5],
        36 => [93.8, 87.6, 100.3],
        48 => [101.2, 94.6, 108.0],
        60 => [108.1, 101.0, 115.5],
    ];

    /**
     * WHO Growth Standards — Head circumference-for-age (boys/girls) in cm.
     *
     * @var array<string, array{float, float, float}>
     */
    private const WHO_HEAD_BOYS = [
        0 => [34.5, 32.0, 37.0],
        3 => [40.3, 37.9, 42.7],
        6 => [43.3, 41.0, 45.7],
        9 => [45.1, 42.8, 47.5],
        12 => [46.3, 43.9, 48.7],
        24 => [48.4, 46.0, 50.8],
        36 => [49.8, 47.3, 52.3],
    ];

    private const WHO_HEAD_GIRLS = [
        0 => [33.9, 31.5, 36.4],
        3 => [39.4, 37.1, 41.8],
        6 => [42.2, 39.9, 44.6],
        9 => [43.9, 41.5, 46.2],
        12 => [45.1, 42.7, 47.5],
        24 => [47.2, 44.8, 49.7],
        36 => [48.7, 46.2, 51.2],
    ];

    /**
     * Get the growth index data for chart rendering.
     *
     * @return array{labels: array<int, string>, weight: array<int, float|null>, height: array<int, float|null>, headCircumference: array<int, float|null>}
     */
    public function getGrowthChartData(Child $child): array
    {
        $growths = $child->growths()
            ->orderBy('measured_at', 'asc')
            ->get();

        $labels = [];
        $weight = [];
        $height = [];
        $headCircumference = [];

        foreach ($growths as $g) {
            $labels[] = $g->measured_at->format('d M Y');
            $weight[] = $g->weight_kg ? (float) $g->weight_kg : null;
            $height[] = $g->height_cm ? (float) $g->height_cm : null;
            $headCircumference[] = $g->head_circumference_cm ? (float) $g->head_circumference_cm : null;
        }

        return compact('labels', 'weight', 'height', 'headCircumference');
    }

    /**
     * Get WHO percentile lines for weight chart.
     *
     * @return array{median: array<int, float|null>, minus2sd: array<int, float|null>, plus2sd: array<int, float|null>}
     */
    public function getWhoWeightPercentiles(string $gender): array
    {
        $data = $gender === 'male' ? self::WHO_WEIGHT_BOYS : self::WHO_WEIGHT_GIRLS;

        return $this->formatWhoPercentiles($data);
    }

    /**
     * Get WHO percentile lines for height chart.
     *
     * @return array{median: array<int, float|null>, minus2sd: array<int, float|null>, plus2sd: array<int, float|null>}
     */
    public function getWhoHeightPercentiles(string $gender): array
    {
        $data = $gender === 'male' ? self::WHO_HEIGHT_BOYS : self::WHO_HEIGHT_GIRLS;

        return $this->formatWhoPercentiles($data);
    }

    /**
     * Get WHO percentile lines for head circumference chart.
     *
     * @return array{median: array<int, float|null>, minus2sd: array<int, float|null>, plus2sd: array<int, float|null>}
     */
    public function getWhoHeadPercentiles(string $gender): array
    {
        $data = $gender === 'male' ? self::WHO_HEAD_BOYS : self::WHO_HEAD_GIRLS;

        return $this->formatWhoPercentiles($data);
    }

    /**
     * Assess a growth measurement against WHO standards.
     *
     * @return array{weightStatus: string, heightStatus: string, headStatus: string}
     */
    public function assessGrowth(Child $child, Growth $growth): array
    {
        $ageInMonths = Carbon::parse($child->date_of_birth)->diffInMonths(now());
        $gender = $child->gender;

        return [
            'weightStatus' => $this->getPercentileStatus(
                $this->getInterpolatedValue($ageInMonths, $gender === 'male' ? self::WHO_WEIGHT_BOYS : self::WHO_WEIGHT_GIRLS, 0),
                $this->getInterpolatedValue($ageInMonths, $gender === 'male' ? self::WHO_WEIGHT_BOYS : self::WHO_WEIGHT_GIRLS, 1),
                $this->getInterpolatedValue($ageInMonths, $gender === 'male' ? self::WHO_WEIGHT_BOYS : self::WHO_WEIGHT_GIRLS, 2),
                $growth->weight_kg ? (float) $growth->weight_kg : null,
            ),
            'heightStatus' => $this->getPercentileStatus(
                $this->getInterpolatedValue($ageInMonths, $gender === 'male' ? self::WHO_HEIGHT_BOYS : self::WHO_HEIGHT_GIRLS, 0),
                $this->getInterpolatedValue($ageInMonths, $gender === 'male' ? self::WHO_HEIGHT_BOYS : self::WHO_HEIGHT_GIRLS, 1),
                $this->getInterpolatedValue($ageInMonths, $gender === 'male' ? self::WHO_HEIGHT_BOYS : self::WHO_HEIGHT_GIRLS, 2),
                $growth->height_cm ? (float) $growth->height_cm : null,
            ),
            'headStatus' => $this->getPercentileStatus(
                $this->getInterpolatedValue($ageInMonths, $gender === 'male' ? self::WHO_HEAD_BOYS : self::WHO_HEAD_GIRLS, 0),
                $this->getInterpolatedValue($ageInMonths, $gender === 'male' ? self::WHO_HEAD_BOYS : self::WHO_HEAD_GIRLS, 1),
                $this->getInterpolatedValue($ageInMonths, $gender === 'male' ? self::WHO_HEAD_BOYS : self::WHO_HEAD_GIRLS, 2),
                $growth->head_circumference_cm ? (float) $growth->head_circumference_cm : null,
            ),
        ];
    }

    /**
     * Format WHO percentile data for chart rendering.
     *
     * @param  array<string, array{float, float, float}>  $data
     * @return array{median: array<int, float|null>, minus2sd: array<int, float|null>, plus2sd: array<int, float|null>}
     */
    private function formatWhoPercentiles(array $data): array
    {
        $median = [];
        $minus2sd = [];
        $plus2sd = [];

        foreach ($data as $month => $values) {
            $median[$month] = $values[0];
            $minus2sd[$month] = $values[1];
            $plus2sd[$month] = $values[2];
        }

        return compact('median', 'minus2sd', 'plus2sd');
    }

    /**
     * Interpolate WHO value at a specific age in months.
     */
    private function getInterpolatedValue(int $ageInMonths, array $whoData, int $index): ?float
    {
        $months = array_keys($whoData);
        sort($months);

        if ($ageInMonths <= $months[0]) {
            return $whoData[$months[0]][$index];
        }

        if ($ageInMonths >= end($months)) {
            return $whoData[end($months)][$index];
        }

        $lower = $months[0];
        $upper = end($months);

        for ($i = 0; $i < count($months) - 1; $i++) {
            if ($ageInMonths >= $months[$i] && $ageInMonths <= $months[$i + 1]) {
                $lower = $months[$i];
                $upper = $months[$i + 1];
                break;
            }
        }

        $ratio = $upper > $lower ? ($ageInMonths - $lower) / ($upper - $lower) : 0;

        return $whoData[$lower][$index] + $ratio * ($whoData[$upper][$index] - $whoData[$lower][$index]);
    }

    /**
     * Determine growth status based on WHO thresholds.
     */
    private function getPercentileStatus(?float $median, ?float $minus2sd, ?float $plus2sd, ?float $value): string
    {
        if ($value === null || $median === null) {
            return 'unknown';
        }

        if ($value < $minus2sd) {
            return 'below_normal';
        }

        if ($value > $plus2sd) {
            return 'above_normal';
        }

        return 'normal';
    }
}
