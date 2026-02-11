<?php

declare(strict_types=1);

namespace App\Actions;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchCountryDetails
{
    /**
     * Mapping of database country codes to REST Countries API codes
     * This handles cases where our database uses different codes than the ISO 3166-1 alpha-3 standard
     */
    private const CODE_MAPPING = [
        'ROM' => 'ROU', // Romania
        'YUG' => 'SRB', // Yugoslavia -> Serbia (database is outdated)
        'TMP' => 'TLS', // East Timor -> Timor-Leste (TMP is obsolete code)
        'ZAR' => 'COD', // Zaire -> Democratic Republic of the Congo (ZAR is obsolete)
        // Add more mappings here as needed
    ];

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
        // Map database code to REST Countries API code if needed
        $apiCode = self::CODE_MAPPING[$countryCode] ?? $countryCode;

        $cacheKey = 'country.details.'.$countryCode;

        try {
            return Cache::remember($cacheKey, 86400, function () use ($apiCode, $countryCode) {
                $response = Http::withOptions([
                    'verify' => config('app.env') === 'production',
                ])
                    ->timeout(10)
                    ->get('https://restcountries.com/v3.1/alpha/'.$apiCode, [
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
                        // Create reverse mapping for API codes back to database codes
                        $reverseMapping = array_flip(self::CODE_MAPPING);

                        foreach ($borderCountries as $borderCountry) {
                            $apiCode = (string) ($borderCountry['cca3'] ?? '');
                            // Convert API code back to database code if mapping exists
                            $dbCode = $reverseMapping[$apiCode] ?? $apiCode;

                            $borders[] = [
                                'code' => $dbCode,
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
