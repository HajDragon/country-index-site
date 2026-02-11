<?php

declare(strict_types=1);

use App\Actions\FetchCovidStats;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    Cache::flush();
});

test('fetches COVID-19 statistics successfully', function () {
    Http::fake([
        'disease.sh/v3/covid-19/countries/*' => Http::response([
            'country' => 'USA',
            'cases' => 100000000,
            'todayCases' => 50000,
            'deaths' => 1000000,
            'todayDeaths' => 500,
            'recovered' => 98000000,
            'active' => 1000000,
            'critical' => 10000,
            'casesPerOneMillion' => 300000,
            'deathsPerOneMillion' => 3000,
            'tests' => 500000000,
            'testsPerOneMillion' => 1500000,
            'population' => 331000000,
            'updated' => 1705670400000,
        ], 200),
    ]);

    $action = new FetchCovidStats;
    $result = $action->execute('USA');

    expect($result)->not->toBeNull()
        ->and($result['country'])->toBe('USA')
        ->and($result['cases'])->toBe(100000000)
        ->and($result['todayCases'])->toBe(50000)
        ->and($result['deaths'])->toBe(1000000)
        ->and($result['recovered'])->toBe(98000000)
        ->and($result['active'])->toBe(1000000)
        ->and($result['casesPerMillion'])->toBe(300000.0)
        ->and($result['population'])->toBe(331000000);
});

test('returns null when API request fails', function () {
    Http::fake([
        'disease.sh/v3/covid-19/countries/*' => Http::response([], 404),
    ]);

    $action = new FetchCovidStats;
    $result = $action->execute('InvalidCountry');

    expect($result)->toBeNull();
});

test('caches COVID statistics for 24 hours', function () {
    Http::fake([
        'disease.sh/v3/covid-19/countries/*' => Http::response([
            'country' => 'Canada',
            'cases' => 5000000,
            'todayCases' => 1000,
            'deaths' => 50000,
            'todayDeaths' => 10,
            'recovered' => 4900000,
            'active' => 50000,
            'critical' => 500,
            'casesPerOneMillion' => 130000,
            'deathsPerOneMillion' => 1300,
            'tests' => 70000000,
            'testsPerOneMillion' => 1800000,
            'population' => 38000000,
            'updated' => 1705670400000,
        ], 200),
    ]);

    $action = new FetchCovidStats;
    $result1 = $action->execute('Canada');
    $result2 = $action->execute('Canada');

    expect($result1)->toBe($result2);

    Http::assertSentCount(1);
});

test('handles empty response data gracefully', function () {
    Http::fake([
        'disease.sh/v3/covid-19/countries/*' => Http::response([], 200),
    ]);

    $action = new FetchCovidStats;
    $result = $action->execute('TestCountry');

    expect($result)->toBeNull();
});

test('handles API timeout gracefully', function () {
    Http::fake(function () {
        throw new \Illuminate\Http\Client\ConnectionException('Connection timeout');
    });

    $action = new FetchCovidStats;
    $result = $action->execute('USA');

    expect($result)->toBeNull();
});

test('formats timestamp correctly', function () {
    Http::fake([
        'disease.sh/v3/covid-19/countries/*' => Http::response([
            'country' => 'Germany',
            'cases' => 40000000,
            'todayCases' => 5000,
            'deaths' => 180000,
            'todayDeaths' => 50,
            'recovered' => 39500000,
            'active' => 320000,
            'critical' => 2000,
            'casesPerOneMillion' => 477000,
            'deathsPerOneMillion' => 2150,
            'tests' => 150000000,
            'testsPerOneMillion' => 1790000,
            'population' => 84000000,
            'updated' => 1705670400000,
        ], 200),
    ]);

    $action = new FetchCovidStats;
    $result = $action->execute('Germany');

    expect($result['updated'])->toBeString()
        ->and($result['updated'])->toMatch('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/');
});
