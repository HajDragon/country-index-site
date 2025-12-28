<?php

declare(strict_types=1);

use App\Models\Country;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('renders SEO tags for country page', function () {
    $user = User::factory()->create();

    // Disable Scout indexing to avoid external Meilisearch dependency during test
    Country::withoutSyncingToSearch(function (): void {
        Country::query()->create([
            'Code' => 'XX',
            'Name' => 'Testland',
            'Continent' => 'Europe',
            'Region' => 'Test Region',
            'Population' => 123456,
            'SurfaceArea' => 1000.00,
            'Code2' => 'TL',
            'latitude' => 0.0000000,
            'longitude' => 0.0000000,
        ]);
    });

    // Record inserted above without Scout syncing

    actingAs($user);

    $response = get(route('country.view', ['countryCode' => 'XX']));

    $response->assertSuccessful();

    // Title should include suffix from config
    $expectedTitle = '<title>Testland | '.config('app.name').'</title>';
    $response->assertSee($expectedTitle, false);

    // Basic description meta tag should be present
    $response->assertSee('<meta name="description"', false);
});
