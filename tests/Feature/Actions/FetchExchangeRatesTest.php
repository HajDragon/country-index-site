<?php

declare(strict_types=1);

use App\Actions\FetchExchangeRates;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    Cache::flush();
});

test('fetches exchange rates successfully', function () {
    Http::fake([
        'api.exchangerate-api.com/*' => Http::response([
            'base' => 'USD',
            'date' => '2026-01-19',
            'rates' => [
                'USD' => 1.0,
                'EUR' => 0.85,
                'GBP' => 0.73,
                'JPY' => 110.5,
                'CAD' => 1.25,
            ],
        ], 200),
    ]);

    $action = new FetchExchangeRates;
    $result = $action->execute('USD');

    expect($result)->not->toBeNull()
        ->and($result['base'])->toBe('USD')
        ->and($result['rates'])->toBeArray()
        ->and($result['usd'])->toBe(1)
        ->and($result['eur'])->toBe(0.85)
        ->and($result['gbp'])->toBe(0.73)
        ->and($result['jpy'])->toBe(110.5);
});

test('returns null when API request fails', function () {
    Http::fake([
        'api.exchangerate-api.com/*' => Http::response([], 500),
    ]);

    $action = new FetchExchangeRates;
    $result = $action->execute('USD');

    expect($result)->toBeNull();
});

test('caches exchange rates for 6 hours', function () {
    Http::fake([
        'api.exchangerate-api.com/*' => Http::response([
            'base' => 'EUR',
            'date' => '2026-01-19',
            'rates' => [
                'USD' => 1.18,
                'EUR' => 1.0,
                'GBP' => 0.86,
                'JPY' => 130.0,
            ],
        ], 200),
    ]);

    $action = new FetchExchangeRates;
    $result1 = $action->execute('EUR');
    $result2 = $action->execute('EUR');

    expect($result1)->toBe($result2);

    Http::assertSentCount(1);
});

test('handles missing rate data gracefully', function () {
    Http::fake([
        'api.exchangerate-api.com/*' => Http::response([
            'base' => 'USD',
            'date' => '2026-01-19',
        ], 200),
    ]);

    $action = new FetchExchangeRates;
    $result = $action->execute('USD');

    expect($result)->toBeNull();
});

test('handles API timeout gracefully', function () {
    Http::fake(function () {
        throw new \Illuminate\Http\Client\ConnectionException('Connection timeout');
    });

    $action = new FetchExchangeRates;
    $result = $action->execute('USD');

    expect($result)->toBeNull();
});
