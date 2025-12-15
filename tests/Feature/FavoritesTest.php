<?php

declare(strict_types=1);

use App\Livewire\CountryCard;
use App\Models\Country;
use App\Models\User;
use App\Models\UserFavorite;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    Country::create([
        'Code' => 'UST',
        'Name' => 'Test Country',
        'Continent' => 'North America',
        'Region' => 'Test Region',
        'SurfaceArea' => 9834000,
        'Population' => 333000000,
        'LifeExpectancy' => 76.4,
        'Code2' => 'US',
    ]);
});

test('authenticated user can add a favorite country', function () {
    $this->actingAs($this->user);

    Livewire::test(CountryCard::class, ['countryCode' => 'UST'])
        ->assertSet('isFavorite', false)
        ->call('toggleFavorite')
        ->assertSet('isFavorite', true);

    $this->assertTrue(
        UserFavorite::where('user_id', $this->user->id)
            ->where('country_code', 'UST')
            ->exists()
    );
});

test('authenticated user can remove a favorite country', function () {
    $this->actingAs($this->user);

    // First add a favorite
    UserFavorite::create([
        'user_id' => $this->user->id,
        'country_code' => 'UST',
    ]);

    Livewire::test(CountryCard::class, ['countryCode' => 'UST'])
        ->assertSet('isFavorite', true)
        ->call('toggleFavorite')
        ->assertSet('isFavorite', false);

    $this->assertFalse(
        UserFavorite::where('user_id', $this->user->id)
            ->where('country_code', 'UST')
            ->exists()
    );
});

test('user can view their favorite countries page', function () {
    $this->actingAs($this->user);

    $response = $this->get('/favorites');
    $response->assertSuccessful();
});

test('unauthenticated user cannot access favorites page', function () {
    $response = $this->get('/favorites');
    $response->assertRedirect('/login');
});

test('unique constraint prevents duplicate favorites', function () {
    $this->actingAs($this->user);

    // Add favorite
    UserFavorite::create([
        'user_id' => $this->user->id,
        'country_code' => 'UST',
    ]);

    // Try to add the same favorite again
    $this->expectException(\Illuminate\Database\QueryException::class);
    UserFavorite::create([
        'user_id' => $this->user->id,
        'country_code' => 'UST',
    ]);
});
