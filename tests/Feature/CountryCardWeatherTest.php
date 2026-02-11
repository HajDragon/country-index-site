<?php

declare(strict_types=1);

use App\Livewire\CountryCard;
use App\Models\Country;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('renders weather details for a country', function (): void {
    Http::fake([
        'https://api.open-meteo.com/v1/forecast*' => Http::response([
            'current' => [
                'temperature_2m' => 20.1,
                'relative_humidity_2m' => 55,
                'weather_code' => 1,
                'wind_speed_10m' => 12,
            ],
        ], 200),
    ]);

    $country = Country::factory()->create([
        'Code' => 'AAA',
        'latitude' => 10.0,
        'longitude' => 20.0,
    ]);

    Livewire::test(CountryCard::class, ['countryCode' => $country->Code])
        ->assertSet('countryCode', 'AAA')
        ->assertSee('Partly cloudy')
        ->assertSee('55% humidity')
        ->assertSee('12 km/h wind');
});
