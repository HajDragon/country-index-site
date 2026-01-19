<?php

use App\Events\CountryInteracted;
use App\Models\Country;
use App\Models\CountryInteraction;
use App\Models\User;
use App\Services\AnalyticsService;
use Illuminate\Support\Facades\Event;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('country interaction event creates database record', function () {
    $country = Country::factory()->create();

    Event::dispatch(new CountryInteracted($country, 'view', $this->user));

    // Process queued listeners
    $this->artisan('queue:work', ['--once' => true]);

    assertDatabaseHas('country_interactions', [
        'country_id' => $country->Code,
        'user_id' => $this->user->id,
        'interaction_type' => 'view',
    ]);
});

test('analytics service returns most searched countries', function () {
    $country1 = Country::factory()->create();
    $country2 = Country::factory()->create();

    // Create search interactions
    CountryInteraction::factory()->count(5)->create([
        'country_id' => $country1->Code,
        'interaction_type' => 'search',
    ]);

    CountryInteraction::factory()->count(3)->create([
        'country_id' => $country2->Code,
        'interaction_type' => 'search',
    ]);

    $service = app(AnalyticsService::class);
    $mostSearched = $service->getMostSearchedCountries(10, 'all');

    expect($mostSearched)->toHaveCount(2);
    expect($mostSearched->first()['country']->Code)->toBe($country1->Code);
    expect($mostSearched->first()['count'])->toBe(5);
});

test('analytics service returns trending countries', function () {
    $country = Country::factory()->create();

    // Create recent interactions (last 7 days)
    CountryInteraction::factory()->count(10)->create([
        'country_id' => $country->Code,
        'interaction_type' => 'view',
        'created_at' => now()->subDays(3),
    ]);

    // Create previous interactions (7-14 days ago)
    CountryInteraction::factory()->count(5)->create([
        'country_id' => $country->Code,
        'interaction_type' => 'view',
        'created_at' => now()->subDays(10),
    ]);

    $service = app(AnalyticsService::class);
    $trending = $service->getTrendingCountries(10);

    expect($trending)->not->toBeEmpty();
    expect($trending->first()['country']->Code)->toBe($country->Code);
    expect($trending->first()['percentage_change'])->toBeGreaterThan(0);
});

test('analytics service returns user country visits', function () {
    $country = Country::factory()->create();

    CountryInteraction::factory()->count(3)->create([
        'country_id' => $country->Code,
        'user_id' => $this->user->id,
        'interaction_type' => 'view',
    ]);

    $service = app(AnalyticsService::class);
    $visits = $service->getUserCountryVisits($this->user);

    expect($visits)->toHaveCount(1);
    expect($visits->first()['country']->Code)->toBe($country->Code);
    expect($visits->first()['visit_count'])->toBe(3);
});

test('analytics service returns regional insights', function () {
    $asiaCountry = Country::factory()->create(['Continent' => 'Asia']);
    $europeCountry = Country::factory()->create(['Continent' => 'Europe']);

    CountryInteraction::factory()->count(5)->create([
        'country_id' => $asiaCountry->Code,
        'user_id' => $this->user->id,
    ]);

    CountryInteraction::factory()->count(3)->create([
        'country_id' => $europeCountry->Code,
        'user_id' => $this->user->id,
    ]);

    $service = app(AnalyticsService::class);
    $insights = $service->getRegionalInsights();

    expect($insights)->toHaveCount(2);
    expect($insights->first()['continent'])->toBe('Asia');
    expect($insights->first()['total_interactions'])->toBe(5);
});

test('analytics dashboard page is accessible to authenticated users', function () {
    actingAs($this->user)
        ->get(route('analytics'))
        ->assertSuccessful()
        ->assertSee('Analytics Dashboard');
});

test('analytics dashboard displays overall statistics', function () {
    $country = Country::factory()->create();

    CountryInteraction::factory()->count(10)->create([
        'country_id' => $country->Code,
        'interaction_type' => 'view',
    ]);

    actingAs($this->user)
        ->get(route('analytics'))
        ->assertSuccessful()
        ->assertSee('Total Interactions')
        ->assertSee('10');
});

test('country view interaction is tracked', function () {
    $country = Country::factory()->create();

    Event::fake();

    actingAs($this->user)
        ->get(route('country.view', ['countryCode' => $country->Code]));

    Event::assertDispatched(CountryInteracted::class, function ($event) use ($country) {
        return $event->country->Code === $country->Code
            && $event->interactionType === 'view'
            && $event->user->id === $this->user->id;
    });
});
