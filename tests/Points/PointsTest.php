<?php

use ProjectSaturnStudios\Weatherman\Exceptions\WeathermanException;
use ProjectSaturnStudios\Weatherman\Forecast\DataObjects\Forecast;
use ProjectSaturnStudios\Weatherman\Points\DataObjects\Point;
use ProjectSaturnStudios\Weatherman\Stations\DataObjects\StationPage;
use Voyager\Contracts\IOPools\Promise;
use Voyager\Http\Client\Factory;

it('rounds a point to four decimal places and hydrates the grid and the city', function () {
    $http = weathermanHttp();
    $http->fake(fn () => Factory::response(weathermanFixture('Points', 'point')));

    $point = weathermanClient($http)->points()->at(38.88944, -77.03524)->get();

    expect($point)->toBeInstanceOf(Point::class)
        ->and($point->grid_id)->toBe('LWX')
        ->and($point->grid_x)->toBe(97)
        ->and($point->grid_y)->toBe(71)
        ->and($point->time_zone)->toBe('America/New_York')
        ->and($point->knownKind()?->value)->toBe('land')
        ->and($point->relative_location->city)->toBe('Washington')
        ->and($point->relative_location->state)->toBe('DC')
        ->and($point->geometry->latitude())->toBe(38.8894)
        ->and($point->geometry->longitude())->toBe(-77.0352)
        ->and($point->radio->transmitter)->toBe('WNG736')
        ->and($point->astronomical_data->sunrise)->toBe('2026-10-04T11:08:00+00:00')
        ->and($point->forecast_url)->toBe('https://api.weather.gov/gridpoints/LWX/97,71/forecast');

    $http->assertSent(fn ($request) => $request->url() === 'https://api.weather.gov/points/38.8894,-77.0352');
});

it('follows the forecast, hourly, and station links the point document publishes', function () {
    $http = weathermanHttp();
    $http->fake(function ($request) {
        $url = $request->url();

        if (str_contains($url, '/forecast/hourly')) {
            return Factory::response(weathermanFixture('Forecast', 'hourly'));
        }

        if (str_contains($url, '/forecast')) {
            return Factory::response(weathermanFixture('Forecast', 'forecast'));
        }

        if (str_contains($url, '/stations')) {
            return Factory::response(weathermanFixture('Stations', 'page'));
        }

        return Factory::response(weathermanFixture('Points', 'point'));
    });

    $client = weathermanClient($http);
    $point = $client->points()->at(38.8894, -77.0352)->get();

    expect($point->forecast()->get())->toBeInstanceOf(Forecast::class)
        ->and($point->forecast()->get()->periods->first()->name)->toBe('Tonight')
        ->and($point->hourly()->get()->periods->first()->dewpoint->value)->toBe(15.6)
        ->and($point->stations()->get())->toBeInstanceOf(StationPage::class)
        ->and($point->stations()->get()->stations)->toHaveCount(2);
});

it('refuses a latitude outside the range', function () {
    expect(fn () => weathermanClient(weathermanHttp(loop: false))->points()->at(91, -77))
        ->toThrow(InvalidArgumentException::class, 'Latitude');
});

it('refuses to follow links on a point hydrated without a client', function () {
    $point = Point::fromArray(weathermanFixture('Points', 'point'));

    expect(fn () => $point->forecast())->toThrow(WeathermanException::class, 'without an NWS client');
});

it('sends the point lookup on the loop', function () {
    $http = weathermanHttp();
    $http->fake(fn () => Factory::response(weathermanFixture('Points', 'point')));

    $promise = weathermanClient($http)->points()->at(38.8894, -77.0352)->async();

    expect($promise)->toBeInstanceOf(Promise::class)
        ->and($promise->wait()->cwa)->toBe('LWX');
});
