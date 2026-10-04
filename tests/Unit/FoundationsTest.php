<?php

use ProjectSaturnStudios\Weatherman\Enums\ForecastOffice;
use ProjectSaturnStudios\Weatherman\Enums\NwsURL;
use ProjectSaturnStudios\Weatherman\Exceptions\WeathermanException;
use ProjectSaturnStudios\Weatherman\NwsClient;
use Voyager\Http\Client\Factory;

it('catalogues the weather.gov host as an uppercase string-backed case', function () {
    foreach (NwsURL::cases() as $case) {
        expect($case->name)->toMatch('/^[A-Z][A-Z0-9_]*$/')
            ->and($case->value)->toStartWith('https://');
    }

    expect(NwsURL::API->value)->toBe('https://api.weather.gov');
});

it('catalogues every forecast office as an uppercase string-backed case', function () {
    expect(ForecastOffice::LWX->value)->toBe('LWX');

    foreach (ForecastOffice::cases() as $case) {
        expect($case->name)->toMatch('/^[A-Z][A-Z0-9_]*$/')
            ->and($case->value)->toBe($case->name);
    }
});

it('reaches the NwsClient through the nws() helper', function () {
    $GLOBALS['__weatherman_test_bindings']['nws'] = $client = weathermanClient(weathermanHttp());

    expect(nws())->toBe($client)->toBeInstanceOf(NwsClient::class);
});

it('sends a User-Agent and application/geo+json on every request', function () {
    $http = weathermanHttp(loop: false);
    $http->fake(fn () => Factory::response(weathermanFixture('Points', 'point')));

    weathermanClient($http)->points()->at(38.8894, -77.0352)->get();

    $http->assertSent(fn ($request) => $request->hasHeader('User-Agent', 'projectsaturnstudios/venusian-weatherman 0.10.0 (https://projectsaturnstudios.com)')
        && $request->hasHeader('Accept', 'application/geo+json')
        && str_contains($request->url(), 'https://api.weather.gov/points/38.8894,-77.0352'));
});

it('refuses to call the weather service without a User-Agent', function () {
    $http = weathermanHttp(loop: false);

    expect(fn () => (new NwsClient(http: $http))->points()->at(38.8894, -77.0352)->get())
        ->toThrow(WeathermanException::class, 'User-Agent');
});

it('carries the HTTP status on a failed request', function () {
    $http = weathermanHttp(loop: false);
    $http->fake(['*' => $http::response('slow down', 429)]);

    expect(fn () => weathermanClient($http)->points()->at(38.8894, -77.0352)->get())
        ->toThrow(fn (WeathermanException $e) => expect($e->status())->toBe(429));
});

it('has no status when no response was involved', function () {
    expect(WeathermanException::loopNotBound()->status())->toBeNull();
});
