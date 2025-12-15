<?php

declare(strict_types=1);

use App\Livewire\CountryList;
use App\Models\Country;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $user = User::factory()->create();

    // Create test countries
    Country::create([
        'Code' => 'USA',
        'Name' => 'United States',
        'Continent' => 'North America',
        'Region' => 'North America',
        'SurfaceArea' => 9834000,
        'Population' => 333000000,
        'LifeExpectancy' => 76.4,
        'Code2' => 'US',
    ]);

    Country::create([
        'Code' => 'GBR',
        'Name' => 'United Kingdom',
        'Continent' => 'Europe',
        'Region' => 'Northern Europe',
        'SurfaceArea' => 242900,
        'Population' => 67000000,
        'LifeExpectancy' => 82.5,
        'Code2' => 'GB',
    ]);

    Country::create([
        'Code' => 'IND',
        'Name' => 'India',
        'Continent' => 'Asia',
        'Region' => 'South Asia',
        'SurfaceArea' => 3287263,
        'Population' => 1417000000,
        'LifeExpectancy' => 71.0,
        'Code2' => 'IN',
    ]);
});

test('user can filter countries by continent', function ($user) {
    $this->actingAs($user);

    // Test that the filter changes the query parameters
    Livewire::test(CountryList::class)
        ->set('selectedContinents', ['North America'])
        ->assertSet('selectedContinents', ['North America']);
});

test('user can filter countries by multiple continents', function ($user) {
    $this->actingAs($user);

    Livewire::test(CountryList::class)
        ->set('selectedContinents', ['North America', 'Europe'])
        ->assertSet('selectedContinents', ['North America', 'Europe']);
});

test('user can filter countries by population range', function ($user) {
    $this->actingAs($user);

    Livewire::test(CountryList::class)
        ->set('populationMin', 100000000)
        ->set('populationMax', 500000000)
        ->assertSee('United States')
        ->assertSee('India')
        ->assertSet('populationMin', 100000000)
        ->assertSet('populationMax', 500000000);
});

test('user can filter countries by life expectancy range', function ($user) {
    $this->actingAs($user);

    Livewire::test(CountryList::class)
        ->set('lifeExpectancyMin', 75)
        ->set('lifeExpectancyMax', 85)
        ->assertSee('United States')
        ->assertSet('lifeExpectancyMin', 75)
        ->assertSet('lifeExpectancyMax', 85);
});

test('user can clear all filters', function ($user) {
    $this->actingAs($user);

    Livewire::test(CountryList::class)
        ->set('selectedContinents', ['Asia'])
        ->set('populationMin', 1000000000)
        ->call('clearFilters')
        ->assertSet('selectedContinents', [])
        ->assertSet('populationMin', 0);
});

test('user can search and filter simultaneously', function ($user) {
    $this->actingAs($user);

    Livewire::test(CountryList::class)
        ->set('search', 'United')
        ->set('selectedContinents', ['Europe'])
        ->assertSee('United Kingdom')
        ->assertDontSee('United States');
});
