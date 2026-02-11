<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Actions\FetchWeatherData;
use App\Models\Country;
use Illuminate\View\View;
use Livewire\Component;

class WeatherCard extends Component
{
    public string $countryCapital = '';

    public ?float $latitude = null;

    public ?float $longitude = null;

    public string $countryName = '';

    public string $countryCode = '';

    public ?array $weatherData = null;

    public bool $loading = true;

    public ?string $error = null;

    public ?string $timezone = null;

    public function mount(): void
    {
        if ($this->countryCode && ($this->countryCapital === '' || $this->countryName === '' || $this->latitude === null || $this->longitude === null)) {
            $this->fetchCountryCapital();
        }

        if ($this->latitude !== null && $this->longitude !== null) {
            $this->fetchWeather();
        } else {
            $this->loading = false;
        }
    }

    public function fetchCountryCapital(): void
    {
        if (! $this->countryCode) {
            return;
        }

        $country = Country::with('capitalCity')
            ->where('Code', $this->countryCode)
            ->first();

        if (! $country) {
            $this->error = 'Country not found';
            $this->loading = false;

            return;
        }

        $this->countryName = (string) $country->Name;
        $this->countryCapital = (string) ($country->capitalCity?->Name ?? '');
        $this->timezone = $country->getPrimaryTimezone();

        // Use country-level coordinates (seeded)
        if ($this->latitude === null && $this->longitude === null && $country->latitude !== null && $country->longitude !== null) {
            $this->latitude = (float) $country->latitude;
            $this->longitude = (float) $country->longitude;
        }
    }

    public function fetchWeather(): void
    {
        $this->loading = true;
        $this->error = null;

        try {
            $fetcher = new FetchWeatherData;
            $this->weatherData = $fetcher->execute($this->latitude, $this->longitude, [
                'with_daily' => true,
                'daily_days' => 5,
            ]);

            if ($this->weatherData === null) {
                $this->error = 'Unable to fetch weather data';
            }
        } catch (\Exception $e) {
            $this->error = 'Weather service unavailable';
        } finally {
            $this->loading = false;
        }
    }

    public function render(): View
    {
        return view('livewire.weather-card');
    }
}
