<?php

declare(strict_types=1);

namespace App\Actions;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class FetchWeatherData
{
    /**
     * Fetch weather data from Open-Meteo API
     *
     * @param  array{with_daily?: bool, daily_days?: int, with_hourly?: bool}  $options
     * @return array{
     *   temperature: float,
     *   humidity: int,
     *   windSpeed: float,
     *   weatherCode: int,
     *   description: string,
     *   icon: string,
     *   daily?: array<int, array{date: string, tmax: float, tmin: float, code: int, description: string, icon: string}>
     * }|null
     */
    public function execute(float $latitude, float $longitude, array $options = []): ?array
    {
        $withDaily = (bool) ($options['with_daily'] ?? false);
        $dailyDays = (int) ($options['daily_days'] ?? 5);
        $withHourly = (bool) ($options['with_hourly'] ?? false);

        $cacheKey = 'weather.v2.'.
            $latitude.'.'.
            $longitude.
            '.daily_'.($withDaily ? $dailyDays : '0').
            '.hourly_'.($withHourly ? '1' : '0');

        try {
            return Cache::remember($cacheKey, 3600, function () use ($latitude, $longitude, $withDaily, $dailyDays, $withHourly) {
                $response = Http::withOptions([
                    'verify' => config('services.http.verify'),
                ])
                    ->timeout(10)
                    ->get('https://api.open-meteo.com/v1/forecast', array_filter([
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                        'current' => 'temperature_2m,relative_humidity_2m,weather_code,wind_speed_10m',
                        'daily' => $withDaily ? 'temperature_2m_max,temperature_2m_min,weather_code' : null,
                        'hourly' => $withHourly ? 'temperature_2m,precipitation_probability,weather_code' : null,
                        'forecast_days' => $withDaily || $withHourly ? $dailyDays + 1 : null,
                        'timezone' => 'auto',
                    ], static fn ($v) => $v !== null));

                if (! $response->successful()) {
                    Log::warning('Weather API request failed', [
                        'status' => $response->status(),
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                    ]);

                    return null;
                }

                $data = $response->json();
                $current = $data['current'] ?? [];

                if (empty($current)) {
                    return null;
                }

                $weatherCode = $current['weather_code'] ?? 0;

                $result = [
                    'temperature' => $current['temperature_2m'] ?? 0,
                    'humidity' => $current['relative_humidity_2m'] ?? 0,
                    'windSpeed' => $current['wind_speed_10m'] ?? 0,
                    'weatherCode' => $weatherCode,
                    'description' => $this->getWeatherDescription($weatherCode),
                    'icon' => $this->getWeatherIcon($weatherCode),
                ];

                // Parse daily forecast if requested
                if ($withDaily && isset($data['daily'])) {
                    $timezone = (string) ($data['timezone'] ?? 'UTC');
                    $result['daily'] = $this->parseDaily($data['daily'], $timezone, $dailyDays);
                }

                return $result;
            });
        } catch (Throwable $e) {
            Log::error('Weather API exception', [
                'message' => $e->getMessage(),
                'latitude' => $latitude,
                'longitude' => $longitude,
            ]);

            return null;
        }
    }

    /**
     * @param  array{time: array<int, string>, temperature_2m_max: array<int, float>, temperature_2m_min: array<int, float>, weather_code: array<int, int>}  $daily
     * @return array<int, array{date: string, tmax: float, tmin: float, code: int, description: string, icon: string}>
     */
    protected function parseDaily(array $daily, string $timezone, int $limit): array
    {
        $result = [];
        $times = $daily['time'] ?? [];
        $tmax = $daily['temperature_2m_max'] ?? [];
        $tmin = $daily['temperature_2m_min'] ?? [];
        $codes = $daily['weather_code'] ?? [];

        $today = Carbon::now($timezone)->toDateString();
        $count = min(count($times), count($tmax), count($tmin), count($codes));
        for ($i = 0; $i < $count; $i++) {
            $code = (int) ($codes[$i] ?? 0);
            $date = (string) ($times[$i] ?? '');

            if ($date === $today) {
                continue;
            }

            $result[] = [
                'date' => $date,
                'tmax' => (float) ($tmax[$i] ?? 0),
                'tmin' => (float) ($tmin[$i] ?? 0),
                'code' => $code,
                'description' => $this->getWeatherDescription($code),
                'icon' => $this->getWeatherIcon($code),
            ];

            if (count($result) >= $limit) {
                break;
            }
        }

        return $result;
    }

    /**
     * Get human-readable weather description from WMO weather code
     */
    protected function getWeatherDescription(int $code): string
    {
        return match (true) {
            $code === 0 => 'Clear sky',
            in_array($code, [1, 2, 3]) => 'Partly cloudy',
            in_array($code, [45, 48]) => 'Foggy',
            in_array($code, [51, 53, 55]) => 'Drizzle',
            in_array($code, [56, 57]) => 'Freezing drizzle',
            in_array($code, [61, 63, 65]) => 'Rain',
            in_array($code, [66, 67]) => 'Freezing rain',
            in_array($code, [71, 73, 75]) => 'Snow',
            in_array($code, [77]) => 'Snow grains',
            in_array($code, [80, 81, 82]) => 'Rain showers',
            in_array($code, [85, 86]) => 'Snow showers',
            in_array($code, [95]) => 'Thunderstorm',
            in_array($code, [96, 99]) => 'Thunderstorm with hail',
            default => 'Unknown',
        };
    }

    /**
     * Get weather icon emoji from WMO weather code
     */
    protected function getWeatherIcon(int $code): string
    {
        return match (true) {
            $code === 0 => '☀️',
            in_array($code, [1, 2, 3]) => '⛅',
            in_array($code, [45, 48]) => '🌫️',
            in_array($code, [51, 53, 55, 56, 57]) => '🌦️',
            in_array($code, [61, 63, 65, 66, 67, 80, 81, 82]) => '🌧️',
            in_array($code, [71, 73, 75, 77, 85, 86]) => '❄️',
            in_array($code, [95, 96, 99]) => '⛈️',
            default => '🌡️',
        };
    }
}
