<?php

declare(strict_types=1);

namespace App\Actions;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchCovidStats
{
    /**
     * Fetch COVID-19 statistics from disease.sh API
     *
     * @return array{
     *   country: string,
     *   cases: int,
     *   todayCases: int,
     *   deaths: int,
     *   todayDeaths: int,
     *   recovered: int,
     *   active: int,
     *   critical: int,
     *   casesPerMillion: float,
     *   deathsPerMillion: float,
     *   tests: int,
     *   testsPerMillion: float,
     *   population: int,
     *   updated: string
     * }|null
     */
    public function execute(string $countryName): ?array
    {
        $cacheKey = 'covid_stats.'.str_replace(' ', '_', strtolower($countryName));

        try {
            return Cache::remember($cacheKey, 86400, function () use ($countryName) {
                $response = Http::withOptions([
                    'verify' => config('app.env') === 'production',
                ])
                    ->timeout(10)
                    ->get('https://disease.sh/v3/covid-19/countries/'.urlencode($countryName), [
                        'strict' => 'true',
                    ]);

                if (! $response->successful()) {
                    Log::warning('COVID-19 API request failed', [
                        'status' => $response->status(),
                        'country' => $countryName,
                    ]);

                    return null;
                }

                $data = $response->json();

                if (empty($data)) {
                    return null;
                }

                return [
                    'country' => $data['country'] ?? $countryName,
                    'cases' => $data['cases'] ?? 0,
                    'todayCases' => $data['todayCases'] ?? 0,
                    'deaths' => $data['deaths'] ?? 0,
                    'todayDeaths' => $data['todayDeaths'] ?? 0,
                    'recovered' => $data['recovered'] ?? 0,
                    'active' => $data['active'] ?? 0,
                    'critical' => $data['critical'] ?? 0,
                    'casesPerMillion' => (float) ($data['casesPerOneMillion'] ?? 0),
                    'deathsPerMillion' => (float) ($data['deathsPerOneMillion'] ?? 0),
                    'tests' => $data['tests'] ?? 0,
                    'testsPerMillion' => (float) ($data['testsPerOneMillion'] ?? 0),
                    'population' => $data['population'] ?? 0,
                    'updated' => isset($data['updated']) ? date('Y-m-d H:i:s', (int) ($data['updated'] / 1000)) : now()->toDateTimeString(),
                ];
            });
        } catch (\Exception $e) {
            Log::error('COVID-19 API error', [
                'country' => $countryName,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
