<?php

declare(strict_types=1);

namespace App\Actions;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchCountryDetails
{
    /**
     * Fetch country details including borders and timezone from REST Countries API
     *
     * @return array{
     *   borders: array<int, array{code: string, name: string}>,
     *   timezones: array<int, string>
     * }|null
     */
    public function execute(string $countryCode): ?array
    {
        $cacheKey = 'country.details.'.$countryCode;

        try {
            return Cache::remember($cacheKey, 86400, function () use ($countryCode) {
                $response = Http::withOptions([
                    'verify' => config('app.env') === 'production',
                ])
                    ->timeout(10)
                    ->get('https://restcountries.com/v3.1/alpha/'.$countryCode, [
                        'fields' => 'borders,timezones,cca3',
                    ]);

                if (! $response->successful()) {
                    Log::warning('REST Countries API request failed', [
                        'status' => $response->status(),
                        'country_code' => $countryCode,
                    ]);

                    return null;
                }

                $data = $response->json();

                if (empty($data) || ! is_array($data)) {
                    return null;
                }

                $country = (is_array($data) && isset($data[0])) ? $data[0] : $data;

                $borders = [];
                if (! empty($country['borders']) && is_array($country['borders'])) {
                    // Fetch names for border countries in bulk
                    $borderCodes = $country['borders'];
                    $bordersResponse = Http::withOptions([
                        'verify' => config('app.env') === 'production',
                    ])
                        ->timeout(10)
                        ->get('https://restcountries.com/v3.1/alpha', [
                            'codes' => implode(',', $borderCodes),
                            'fields' => 'cca3,name',
                        ]);

                    if ($bordersResponse->successful()) {
                        $borderCountries = $bordersResponse->json();
                        foreach ($borderCountries as $borderCountry) {
                            $borders[] = [
                                'code' => (string) ($borderCountry['cca3'] ?? ''),
                                'name' => (string) ($borderCountry['name']['common'] ?? ''),
                            ];
                        }
                    }
                }

                return [
                    'borders' => $borders,
                    'timezones' => $country['timezones'] ?? [],
                ];
            });
        } catch (\Exception $e) {
            Log::error('REST Countries API exception', [
                'message' => $e->getMessage(),
                'country_code' => $countryCode,
            ]);

            return null;
        }
    }
}
