<?php

declare(strict_types=1);

use App\Models\Country;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();

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
});

test('authenticated user can access statistics page', function () {
    $this->actingAs($this->user)
        ->get('/stats')
        ->assertSuccessful();
});

test('statistics page displays total countries', function () {
    $this->actingAs($this->user)
        ->get('/stats')
        ->assertSee('Total Countries')
        ->assertSee('2');
});

test('statistics page displays average population', function () {
    $this->actingAs($this->user)
        ->get('/stats')
        ->assertSee('Avg Population');
});

test('statistics page displays average life expectancy', function () {
    $this->actingAs($this->user)
        ->get('/stats')
        ->assertSee('Avg Life Expectancy');
});

test('statistics page displays continent breakdown', function () {
    $this->actingAs($this->user)
        ->get('/stats')
        ->assertSee('North America')
        ->assertSee('Europe');
});

test('statistics page displays top countries', function () {
    $this->actingAs($this->user)
        ->get('/stats')
        ->assertSee('Top 10 Countries by Population')
        ->assertSee('United States');
});

test('unauthenticated user cannot access statistics page', function () {
    $this->get('/stats')
        ->assertRedirect('/login');
});
