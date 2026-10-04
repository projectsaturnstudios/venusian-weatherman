<?php

use ProjectSaturnStudios\Weatherman\Enums\ForecastOffice;
use ProjectSaturnStudios\Weatherman\Enums\QualityControl;
use ProjectSaturnStudios\Weatherman\Observations\DataObjects\Observation;
use ProjectSaturnStudios\Weatherman\Stations\DataObjects\Station;
use ProjectSaturnStudios\Weatherman\Stations\DataObjects\StationPage;
use Voyager\Contracts\IOPools\Promise;
use Voyager\Http\Client\Factory;

it('hydrates a station and its latest observation', function () {
    $http = weathermanHttp();
    $http->fake(function ($request) {
        if (str_contains($request->url(), '/observations/latest')) {
            return Factory::response(weathermanFixture('Observations', 'latest'));
        }

        return Factory::response(weathermanFixture('Stations', 'station'));
    });

    $client = weathermanClient($http);
    $station = $client->stations()->find('kdca')->get();
    $observation = $station->latest()->get();

    expect($station)->toBeInstanceOf(Station::class)
        ->and($station->station_identifier)->toBe('KDCA')
        ->and($station->name)->toContain('Reagan')
        ->and($station->elevation->value)->toBe(3.9624)
        ->and($observation)->toBeInstanceOf(Observation::class)
        ->and($observation->text_description)->toBe('Cloudy')
        ->and($observation->temperature->value)->toBe(18.0)
        ->and($observation->temperature->knownQuality())->toBe(QualityControl::V)
        ->and($observation->cloud_layers)->toHaveCount(2)
        ->and($observation->cloud_layers->first()->amount)->toBe('BKN');

    $http->assertSent(fn ($request) => str_contains($request->url(), '/stations/KDCA/observations/latest'));
});

it('lists stations for a gridpoint and follows the next page', function () {
    $http = weathermanHttp();
    $calls = 0;
    $http->fake(function () use (&$calls) {
        $calls++;

        if ($calls === 1) {
            return Factory::response(weathermanFixture('Stations', 'page'));
        }

        $page = weathermanFixture('Stations', 'page');
        unset($page['pagination']);

        return Factory::response($page);
    });

    $page = weathermanClient($http)->stations()->grid(ForecastOffice::LWX, 97, 71)->get();

    expect($page)->toBeInstanceOf(StationPage::class)
        ->and($page->stations->last()->station_identifier)->toBe('KIAD')
        ->and($page->observation_station_urls)->toHaveCount(2)
        ->and($page->next()->get()->next_url)->toBeNull();

    $http->assertSent(fn ($request) => str_contains($request->url(), 'https://api.weather.gov/gridpoints/LWX/97,71/stations')
        || str_contains($request->url(), 'cursor=next-page'));
});

it('stops walking station pages when the next page is empty even if it still has a next link', function () {
    $http = weathermanHttp();
    $calls = 0;
    $http->fake(function () use (&$calls) {
        $calls++;

        if ($calls === 1) {
            return Factory::response(weathermanFixture('Stations', 'page'));
        }

        return Factory::response([
            'type' => 'FeatureCollection',
            'features' => [],
            'observationStations' => [],
            'pagination' => ['next' => 'https://api.weather.gov/stations?cursor=keeps-going'],
        ]);
    });

    $page = weathermanClient($http)->stations()->grid(ForecastOffice::LWX, 97, 71)->get();

    expect($page->next())->not->toBeNull()
        ->and($page->next()->get()->stations)->toHaveCount(0)
        ->and($page->next()->get()->next())->toBeNull();
});

it('filters the station index by state and refuses a limit outside 1 to 500', function () {
    $http = weathermanHttp();
    $http->fake(fn () => Factory::response(weathermanFixture('Stations', 'page')));
    $stations = weathermanClient($http)->stations();

    $stations->index('va', 50)->get();

    $http->assertSent(fn ($request) => str_contains($request->url(), 'stations?')
        && str_contains($request->url(), 'state=VA')
        && str_contains($request->url(), 'limit=50'));

    expect(fn () => $stations->index(limit: 501))->toThrow(InvalidArgumentException::class, '1 to 500');
});

it('sends require_qc and the latest observation on the loop', function () {
    $http = weathermanHttp();
    $http->fake(fn () => Factory::response(weathermanFixture('Observations', 'latest')));

    $promise = weathermanClient($http)->observations()->latest('KDCA', require_qc: true)->async();

    expect($promise)->toBeInstanceOf(Promise::class)
        ->and($promise->wait()->station_id)->toBe('KDCA');

    $http->assertSent(fn ($request) => str_contains($request->url(), 'require_qc=true'));
});
