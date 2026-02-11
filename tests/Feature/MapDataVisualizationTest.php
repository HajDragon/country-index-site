<?php

use App\Models\Country;
use App\Models\User;
use App\Services\MapDataService;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('map data service returns formatted data', function () {
    $country = Country::factory()->create([
        'Population' => 100000000,
        'LifeExpectancy' => 75.5,
        'GNP' => 500000,
        'latitude' => 40.7128,
        'longitude' => -74.0060,
    ]);

    $service = app(MapDataService::class);
    $data = $service->getMapData('population');

    expect($data)->toBeArray()
        ->and($data)->not->toBeEmpty();

    $countryData = collect($data)->firstWhere('code', $country->Code);

    expect($countryData)->toHaveKeys(['code', 'name', 'value', 'lat', 'lng', 'formatted_value'])
        ->and($countryData['code'])->toBe($country->Code)
        ->and($countryData['value'])->toBe((float) $country->Population);
});

test('map data service caches results', function () {
    Country::factory()->count(5)->create([
        'latitude' => 40.0,
        'longitude' => -74.0,
    ]);

    $service = app(MapDataService::class);

    // First call - should cache
    $data1 = $service->getMapData('population');

    // Second call - should use cache
    $data2 = $service->getMapData('population');

    expect($data1)->toEqual($data2);
});

test('map data service provides color scales for metrics', function () {
    $service = app(MapDataService::class);

    $populationScale = $service->getColorScale('population');
    $lifeExpectancyScale = $service->getColorScale('life_expectancy');
    $gdpScale = $service->getColorScale('gdp_per_capita');

    expect($populationScale)->toHaveKeys(['colors', 'breaks'])
        ->and($populationScale['colors'])->toBeArray()
        ->and($populationScale['breaks'])->toBeArray();

    expect($lifeExpectancyScale)->toHaveKeys(['colors', 'breaks']);
    expect($gdpScale)->toHaveKeys(['colors', 'breaks']);
});

test('map data service filters countries without coordinates', function () {
    Country::factory()->create([
        'latitude' => null,
        'longitude' => null,
    ]);

    Country::factory()->create([
        'latitude' => 40.0,
        'longitude' => -74.0,
    ]);

    $service = app(MapDataService::class);
    $data = $service->getMapData('population');

    // Should only include country with coordinates
    expect($data)->toHaveCount(1);
});

test('statistics page renders world map component', function () {
    actingAs($this->user)
        ->get(route('stats'))
        ->assertSuccessful()
        ->assertSeeLivewire('components.interactive-world-map')
        ->assertSee('Interactive World Map');
});

test('world map component can switch metrics', function () {
    Country::factory()->count(5)->create([
        'latitude' => 40.0,
        'longitude' => -74.0,
    ]);

    actingAs($this->user)
        ->get(route('stats'))
        ->assertSuccessful()
        ->assertSee('Population')
        ->assertSee('Life Expectancy')
        ->assertSee('GDP per Capita');
});

test('map data service formats metric values correctly', function () {
    $country = Country::factory()->create([
        'Population' => 50000000,
        'LifeExpectancy' => 78.3,
        'GNP' => 250000,
        'SurfaceArea' => 50000,
        'latitude' => 40.0,
        'longitude' => -74.0,
    ]);

    $service = app(MapDataService::class);

    $populationData = $service->getMapData('population');
    $countryData = collect($populationData)->firstWhere('code', $country->Code);
    expect($countryData['formatted_value'])->toContain('50,000,000');

    $lifeExpectancyData = $service->getMapData('life_expectancy');
    $countryData = collect($lifeExpectancyData)->firstWhere('code', $country->Code);
    expect($countryData['formatted_value'])->toContain('years');
});

test('map data service can clear cache', function () {
    Country::factory()->count(5)->create([
        'latitude' => 40.0,
        'longitude' => -74.0,
    ]);

    $service = app(MapDataService::class);

    // Populate cache
    $service->getMapData('population');

    // Clear cache
    $service->clearCache();

    // Should rebuild cache on next call
    $data = $service->getMapData('population');

    expect($data)->toBeArray()->not->toBeEmpty();
});
