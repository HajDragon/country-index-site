<?php

declare(strict_types=1);

namespace App\Actions;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchExchangeRates
{
    /**
     * Fetch exchange rates from ExchangeRate-API
     *
     * @return array{
     *   base: string,
     *   rates: array<string, float>,
     *   lastUpdate: string,
     *   usd: float,
     *   eur: float,
     *   gbp: float,
     *   jpy: float
     * }|null
     */
    public function execute(string $currencyCode): ?array
    {
        $cacheKey = 'exchange_rates.'.$currencyCode;

        try {
            return Cache::remember($cacheKey, 21600, function () use ($currencyCode) {
                $response = Http::withOptions([
                    'verify' => config('services.http.verify'),
                ])
                    ->timeout(10)
                    ->get("https://api.exchangerate-api.com/v4/latest/{$currencyCode}");

                if (! $response->successful()) {
                    Log::warning('Exchange Rate API request failed', [
                        'status' => $response->status(),
                        'currency' => $currencyCode,
                    ]);

                    return null;
                }

                $data = $response->json();

                if (empty($data['rates'])) {
                    return null;
                }

                return [
                    'base' => $data['base'] ?? $currencyCode,
                    'rates' => $data['rates'] ?? [],
                    'lastUpdate' => $data['date'] ?? now()->toDateString(),
                    'usd' => $data['rates']['USD'] ?? 0,
                    'eur' => $data['rates']['EUR'] ?? 0,
                    'gbp' => $data['rates']['GBP'] ?? 0,
                    'jpy' => $data['rates']['JPY'] ?? 0,
                ];
            });
        } catch (\Exception $e) {
            Log::error('Exchange Rate API error', [
                'currency' => $currencyCode,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
