<?php

namespace App\Services;

use App\Models\Country;
use Illuminate\Support\Facades\Cache;

class MapDataService
{
    /**
     * Get map data for a specific metric
     */
    public function getMapData(string $metric = 'population'): array
    {
        return Cache::remember("map_data_{$metric}", 3600, function () use ($metric) {
            return Country::query()
                ->get()
                ->map(function ($country) use ($metric) {
                    return [
                        'code' => $country->Code,
                        'name' => $country->Name,
                        'value' => $this->getMetricValue($country, $metric),
                        'lat' => $country->latitude ?? 0,
                        'lng' => $country->longitude ?? 0,
                        'formatted_value' => $this->formatMetricValue($country, $metric),
                    ];
                })
                ->filter(fn ($item) => $item['lat'] !== 0 && $item['lng'] !== 0)
                ->values()
                ->toArray();
        });
    }

    /**
     * Get value for a specific metric
     */
    protected function getMetricValue(Country $country, string $metric): float
    {
        return match ($metric) {
            'population' => (float) $country->Population ?? 0,
            'life_expectancy' => (float) $country->LifeExpectancy ?? 0,
            'gdp' => (float) $country->GNP ?? 0,
            'gdp_per_capita' => $country->Population > 0
                ? round(($country->GNP * 1000000) / $country->Population, 2)
                : 0,
            'surface_area' => (float) $country->SurfaceArea ?? 0,
            default => 0,
        };
    }

    /**
     * Format metric value for display
     */
    protected function formatMetricValue(Country $country, string $metric): string
    {
        $value = $this->getMetricValue($country, $metric);

        return match ($metric) {
            'population' => number_format($value),
            'life_expectancy' => number_format($value, 1).' years',
            'gdp' => '$'.number_format($value, 2).'M',
            'gdp_per_capita' => '$'.number_format($value, 2),
            'surface_area' => number_format($value, 2).' km²',
            default => (string) $value,
        };
    }

    /**
     * Get color scale for a metric
     */
    public function getColorScale(string $metric): array
    {
        return match ($metric) {
            'population' => [
                'colors' => ['#f0f9ff', '#bae6fd', '#7dd3fc', '#38bdf8', '#0284c7', '#0369a1'],
                'breaks' => [1000000, 10000000, 50000000, 100000000, 500000000],
            ],
            'life_expectancy' => [
                'colors' => ['#fee2e2', '#fecaca', '#fca5a5', '#f87171', '#ef4444', '#dc2626'],
                'breaks' => [50, 60, 70, 75, 80],
            ],
            'gdp_per_capita' => [
                'colors' => ['#f0fdf4', '#bbf7d0', '#86efac', '#4ade80', '#22c55e', '#16a34a'],
                'breaks' => [1000, 5000, 15000, 30000, 50000],
            ],
            default => [
                'colors' => ['#f3f4f6', '#d1d5db', '#9ca3af', '#6b7280', '#4b5563', '#374151'],
                'breaks' => [0, 20, 40, 60, 80],
            ],
        };
    }

    /**
     * Clear map data cache
     */
    public function clearCache(): void
    {
        $metrics = ['population', 'life_expectancy', 'gdp', 'gdp_per_capita', 'surface_area'];

        foreach ($metrics as $metric) {
            Cache::forget("map_data_{$metric}");
        }
    }
}
