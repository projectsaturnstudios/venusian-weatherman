<?php

use ProjectSaturnStudios\Weatherman\Enums\ForecastFeatureFlag;
use ProjectSaturnStudios\Weatherman\Enums\ForecastOffice;
use ProjectSaturnStudios\Weatherman\Enums\ForecastUnits;
use ProjectSaturnStudios\Weatherman\Enums\TemperatureTrend;
use ProjectSaturnStudios\Weatherman\Forecast\DataObjects\Forecast;
use Voyager\Contracts\IOPools\Promise;
use Voyager\Http\Client\Factory;

it('asks for the 12-hour forecast of an office grid and hydrates both period shapes', function () {
    $http = weathermanHttp();
    $http->fake(fn () => Factory::response(weathermanFixture('Forecast', 'forecast')));

    $forecast = weathermanClient($http)->forecast()->grid(ForecastOffice::LWX, 97, 71)->get();
    $tonight = $forecast->periods->first();
    $sunday = $forecast->periods->last();

    expect($forecast)->toBeInstanceOf(Forecast::class)
        ->and($forecast->units)->toBe('us')
        ->and($forecast->elevation->value)->toBe(6.096)
        ->and($forecast->periods)->toHaveCount(2)
        ->and($tonight->temperature)->toBe(60.0)
        ->and($tonight->temperature_unit)->toBe('F')
        ->and($tonight->temperature_reading)->toBeNull()
        ->and($tonight->wind_speed)->toBe('5 mph')
        ->and($tonight->short_forecast)->toBe('Chance Rain Showers')
        ->and($sunday->knownTrend())->toBe(TemperatureTrend::RISING);

    $http->assertSent(fn ($request) => $request->url() === 'https://api.weather.gov/gridpoints/LWX/97,71/forecast');
});

it('accepts an office id string and sends SI units plus a feature flag', function () {
    $http = weathermanHttp();
    $http->fake(fn () => Factory::response(weathermanFixture('Forecast', 'quantitative')));

    $period = weathermanClient($http)->forecast()
        ->hourly('lwx', 97, 71)
        ->with('units', ForecastUnits::SI)
        ->header('Feature-Flags', ForecastFeatureFlag::FORECAST_TEMPERATURE_QV)
        ->get()
        ->periods
        ->first();

    expect($period->temperature)->toBe(15.6)
        ->and($period->temperature_unit)->toBe('wmoUnit:degC')
        ->and($period->temperature_reading->value)->toBe(15.6)
        ->and($period->wind_speed)->toBeNull()
        ->and($period->windSpeedText())->toBe('8 wmoUnit:km_h-1')
        ->and($period->knownTrend())->toBe(TemperatureTrend::FALLING);

    $http->assertSent(fn ($request) => str_contains($request->url(), '/gridpoints/LWX/97,71/forecast/hourly')
        && str_contains($request->url(), 'units=si')
        && $request->hasHeader('Feature-Flags', 'forecast_temperature_qv'));
});

it('refuses an unknown office and a negative grid axis', function () {
    $forecast = weathermanClient(weathermanHttp(loop: false))->forecast();

    expect(fn () => $forecast->grid('NOPE', 1, 1))->toThrow(InvalidArgumentException::class, 'Unknown NWS forecast office')
        ->and(fn () => $forecast->grid(ForecastOffice::LWX, -1, 1))->toThrow(InvalidArgumentException::class, '0 or greater');
});

it('sends the hourly forecast on the loop', function () {
    $http = weathermanHttp();
    $http->fake(fn () => Factory::response(weathermanFixture('Forecast', 'hourly')));

    $promise = weathermanClient($http)->forecast()->hourly(ForecastOffice::LWX, 97, 71)->async();

    expect($promise)->toBeInstanceOf(Promise::class)
        ->and($promise->wait()->periods->first()->short_forecast)->toBe('Slight Chance Rain Showers');
});
