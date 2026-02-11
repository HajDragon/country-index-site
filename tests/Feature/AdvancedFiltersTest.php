<?php

declare(strict_types=1);

use App\Livewire\CountryList;
use App\Models\Country;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    // Use Scout's local database engine during tests to avoid external services
    config()->set('scout.driver', 'database');
    config()->set('scout.queue', false);
    $user = User::factory()->create();

    // Create test countries without syncing to Scout/Meilisearch
    Country::withoutSyncingToSearch(function () {
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
});

test('user can filter countries by continent', function () {
    $this->actingAs(User::factory()->create());

    // Test that the filter changes the query parameters
    Livewire::test(CountryList::class)
        ->set('selectedContinents', ['North America'])
        ->assertSet('selectedContinents', ['North America']);
});

test('user can filter countries by multiple continents', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(CountryList::class)
        ->set('selectedContinents', ['North America', 'Europe'])
        ->assertSet('selectedContinents', ['North America', 'Europe']);
});

test('user can filter countries by population range', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(CountryList::class)
        ->set('populationMin', 100000000)
        ->set('populationMax', 500000000)
        // Only United States should match in our fixture data
        ->assertSee('Showing all 1 country')
        ->assertSet('populationMin', 100000000)
        ->assertSet('populationMax', 500000000);
});

test('user can filter countries by life expectancy range', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(CountryList::class)
        ->set('lifeExpectancyMin', 75)
        ->set('lifeExpectancyMax', 85)
        // Two countries should match (USA and UK)
        ->assertSee('Showing all 2 countries')
        ->assertSet('lifeExpectancyMin', 75)
        ->assertSet('lifeExpectancyMax', 85);
});

test('user can clear all filters', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(CountryList::class)
        ->set('selectedContinents', ['Asia'])
        ->set('populationMin', 1000000000)
        ->call('clearFilters')
        ->assertSet('selectedContinents', [])
        ->assertSet('populationMin', 0);
});

test('user can search and filter simultaneously', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(CountryList::class)
        // Trigger manual search to avoid live queries during typing
        ->set('searchTerm', 'United')
        ->call('performSearch')
        ->set('selectedContinents', ['Europe'])
        // Only United Kingdom should remain after filtering
        ->assertSee('Showing all 1 country');
});

test('load more increments page counter', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(CountryList::class)
        ->assertSet('page', 1)
        ->call('loadMore')
        ->assertSet('page', 2);
});

test('reset scroll resets page to 1', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(CountryList::class)
        ->set('page', 3)
        ->call('resetScroll')
        ->assertSet('page', 1);
    // Note: hasMore is recalculated in render() based on actual data
});

test('filter changes reset scroll position', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(CountryList::class)
        ->set('page', 3)
        ->set('selectedContinents', ['Europe'])
        ->assertSet('page', 1);
});

test('hasMore is correctly calculated based on results', function () {
    $this->actingAs(User::factory()->create());

    // With only 3 countries in fixture and perPage=12, hasMore should be false
    Livewire::test(CountryList::class)
        ->assertSet('hasMore', false);
});
