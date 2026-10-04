<?php

use ProjectSaturnStudios\Weatherman\Alerts\DataObjects\Alert;
use ProjectSaturnStudios\Weatherman\Alerts\DataObjects\AlertCount;
use ProjectSaturnStudios\Weatherman\Alerts\DataObjects\AlertPage;
use ProjectSaturnStudios\Weatherman\NwsClient;
use ProjectSaturnStudios\Weatherman\Enums\AlertMessageType;
use ProjectSaturnStudios\Weatherman\Enums\AlertQueryStatus;
use ProjectSaturnStudios\Weatherman\Enums\AlertSeverity;
use ProjectSaturnStudios\Weatherman\Enums\AlertStatus;
use ProjectSaturnStudios\Weatherman\Enums\MarineRegion;
use Voyager\Contracts\IOPools\Promise;
use Voyager\Http\Client\Factory;
use Voyager\NutsAndBolts\Collection;

it('hydrates an active alert page and lowercases document status on the query', function () {
    $http = weathermanHttp();
    $http->fake(fn () => Factory::response(weathermanFixture('Alerts', 'active')));

    $page = weathermanClient($http)->alerts()->active()->with('status', AlertStatus::ACTUAL)->get();
    $alert = $page->alerts->first();

    expect($page)->toBeInstanceOf(AlertPage::class)
        ->and($page->title)->toContain('California')
        ->and($alert)->toBeInstanceOf(Alert::class)
        ->and($alert->event)->toBe('Heat Advisory')
        ->and($alert->knownStatus())->toBe(AlertStatus::ACTUAL)
        ->and($alert->knownMessageType())->toBe(AlertMessageType::UPDATE)
        ->and($alert->knownSeverity())->toBe(AlertSeverity::MODERATE)
        ->and($alert->affected_zones->first())->toBe('https://api.weather.gov/zones/forecast/CAZ006')
        ->and($alert->references->first()->identifier)->toBe('urn:oid:2.49.0.1.840.0.example.001.0')
        ->and($alert->parameters['AWIPSidentifier'])->toBe(['NPWMTR'])
        ->and($page->next())->not->toBeNull();

    $http->assertSent(fn ($request) => str_starts_with($request->url(), 'https://api.weather.gov/alerts/active?')
        && str_contains($request->url(), 'status=actual'));
});

it('addresses active alerts by point, area, zone, and marine region', function (string $method, array $args, string $path) {
    $http = weathermanHttp();
    $http->fake(fn () => Factory::response(weathermanFixture('Alerts', 'active')));

    $page = weathermanClient($http)->alerts()->{$method}(...$args)->get();

    expect($page)->toBeInstanceOf(AlertPage::class);
    $http->assertSent(fn ($request) => str_contains($request->url(), $path));
})->with([
    'point' => ['at', [38.8894, -77.0352], 'alerts/active?point=38.8894%2C-77.0352'],
    'area' => ['area', ['ca'], 'alerts/active/area/CA'],
    'zone' => ['zone', ['caz006'], 'alerts/active/zone/CAZ006'],
    'region' => ['region', [MarineRegion::AL], 'alerts/active/region/AL'],
]);

it('hydrates one alert by id without encoding the urn colons', function () {
    $http = weathermanHttp();
    $http->fake(fn () => Factory::response(weathermanFixture('Alerts', 'single')));

    $alert = weathermanClient($http)->alerts()->find('urn:oid:2.49.0.1.840.0.example.001.1')->get();

    expect($alert)->toBeInstanceOf(Alert::class)
        ->and($alert->headline)->toContain('Heat Advisory');

    $http->assertSent(fn ($request) => $request->url() === 'https://api.weather.gov/alerts/urn:oid:2.49.0.1.840.0.example.001.1');
});

it('hydrates the active count and the event type list', function () {
    $http = weathermanHttp();
    $http->fake(function ($request) {
        if (str_contains($request->url(), '/count')) {
            return Factory::response(weathermanFixture('Alerts', 'count'));
        }

        return Factory::response(weathermanFixture('Alerts', 'types'));
    });

    $client = weathermanClient($http);

    expect($client->alerts()->count()->get())->toBeInstanceOf(AlertCount::class)
        ->and($client->alerts()->count()->get()->total)->toBe(426)
        ->and($client->alerts()->count()->get()->areas['CA'])->toBe(19)
        ->and($client->alerts()->types()->get())->toBeInstanceOf(Collection::class)
        ->and($client->alerts()->types()->get()->all())->toBe([
            'Air Quality Alert',
            'Heat Advisory',
            'Tornado Warning',
        ]);
});

it('keeps severity in title case and rejects a zone id that is not UGC', function () {
    $http = weathermanHttp();
    $http->fake(fn () => Factory::response(weathermanFixture('Alerts', 'active')));
    $alerts = weathermanClient($http)->alerts();

    $alerts->active()->with('severity', AlertSeverity::MODERATE)->with('message_type', AlertMessageType::ALERT)->get();

    $http->assertSent(fn ($request) => str_contains($request->url(), 'severity=Moderate')
        && str_contains($request->url(), 'message_type=alert'));

    expect(fn () => $alerts->zone('not-a-zone'))->toThrow(InvalidArgumentException::class, 'UGC');
});

it('does not follow pagination.next on an empty alert page', function () {
    $page = AlertPage::fromArray([
        'type' => 'FeatureCollection',
        'features' => [],
        'pagination' => ['next' => 'https://api.weather.gov/alerts?cursor=keeps-going'],
    ], new NwsClient(user_agent: 'test'));

    expect($page->next())->toBeNull();
});

it('addresses the full alert collection, not only the active set', function () {
    $http = weathermanHttp();
    $http->fake(fn () => Factory::response(weathermanFixture('Alerts', 'active')));

    weathermanClient($http)->alerts()->collection()->with('start', '2026-10-01T00:00:00Z')->get();

    $http->assertSent(fn ($request) => str_starts_with($request->url(), 'https://api.weather.gov/alerts?')
        && ! str_contains($request->url(), '/alerts/active')
        && str_contains($request->url(), 'start=2026-10-01T00%3A00%3A00Z'));
});

it('sends the active alert query on the loop', function () {
    $http = weathermanHttp();
    $http->fake(fn () => Factory::response(weathermanFixture('Alerts', 'active')));

    $promise = weathermanClient($http)->alerts()->active()->with('status', AlertQueryStatus::ACTUAL)->async();

    expect($promise)->toBeInstanceOf(Promise::class)
        ->and($promise->wait()->alerts)->toHaveCount(1);
});
