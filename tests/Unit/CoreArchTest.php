<?php

use ProjectSaturnStudios\Weatherman\Alerts\AlertsAPIService;
use ProjectSaturnStudios\Weatherman\Contracts\HydratesFromArray;
use ProjectSaturnStudios\Weatherman\Enums\NwsURL;
use ProjectSaturnStudios\Weatherman\Exceptions\NotYetSupportedException;
use ProjectSaturnStudios\Weatherman\Exceptions\WeathermanException;
use ProjectSaturnStudios\Weatherman\Forecast\ForecastAPIService;
use ProjectSaturnStudios\Weatherman\NwsApiService;
use ProjectSaturnStudios\Weatherman\NwsClient;
use ProjectSaturnStudios\Weatherman\Observations\ObservationsAPIService;
use ProjectSaturnStudios\Weatherman\PendingNwsRequest;
use ProjectSaturnStudios\Weatherman\Points\PointsAPIService;
use ProjectSaturnStudios\Weatherman\Stations\StationsAPIService;
use Voyager\Contracts\IOPools\Promise;
use Voyager\Http\Client\Factory;
use Voyager\NutsAndBolts\Collection;

final readonly class CoreArchSampleRecord implements HydratesFromArray
{
    public function __construct(
        public string $id,
        public string $name,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            id: (string) $data['id'],
            name: (string) $data['name'],
        );
    }
}

it('exposes NotYetSupportedException as a WeathermanException', function () {
    expect(NotYetSupportedException::forApi('Radar'))
        ->toBeInstanceOf(WeathermanException::class);
});

it('exposes every core API accessor on NwsClient', function () {
    $client = new NwsClient(user_agent: 'test');

    expect($client->points())->toBeInstanceOf(PointsAPIService::class)
        ->and($client->forecast())->toBeInstanceOf(ForecastAPIService::class)
        ->and($client->alerts())->toBeInstanceOf(AlertsAPIService::class)
        ->and($client->stations())->toBeInstanceOf(StationsAPIService::class)
        ->and($client->observations())->toBeInstanceOf(ObservationsAPIService::class)
        ->and($client->points())->toBeInstanceOf(NwsApiService::class);
});

it('hydrates a list endpoint into a Collection of DTOs via get()', function () {
    $http = weathermanHttp();
    $http->fake(fn () => Factory::response([
        ['id' => 'a', 'name' => 'alpha'],
        ['id' => 'b', 'name' => 'beta'],
    ]));

    $result = (new NwsClient(user_agent: 'test', http: $http))
        ->pending(
            base: NwsURL::API,
            path: 'alerts/types',
            call_name: 'weatherman.alerts.types',
            hydrator: CoreArchSampleRecord::class,
        )
        ->get();

    expect($result)->toBeInstanceOf(Collection::class)
        ->and($result)->toHaveCount(2)
        ->and($result->first())->toBeInstanceOf(CoreArchSampleRecord::class)
        ->and($result->first()->id)->toBe('a');
});

it('returns a loop promise from async() that fulfils with hydrated data', function () {
    $http = weathermanHttp();
    $http->fake(fn () => Factory::response(['id' => 'solo', 'name' => 'one']));

    $promise = (new PendingNwsRequest(
        base: NwsURL::API,
        path: 'points/38.8894,-77.0352',
        call_name: 'weatherman.points.at',
        hydrator: CoreArchSampleRecord::class,
        user_agent: 'test',
        http: $http,
    ))->async();

    expect($promise)->toBeInstanceOf(Promise::class)
        ->and($promise->wait())->toBeInstanceOf(CoreArchSampleRecord::class)
        ->and($promise->wait()->name)->toBe('one');
});

it('throws WeathermanException from async() when no loop is bound', function () {
    expect(fn () => (new PendingNwsRequest(
        base: NwsURL::API,
        path: 'alerts/active',
        call_name: 'weatherman.alerts.active',
        hydrator: CoreArchSampleRecord::class,
        user_agent: 'test',
        http: weathermanHttp(loop: false),
    ))->async())->toThrow(WeathermanException::class, 'loop');
});

it('lets fluent with() replace query params on the pending request', function () {
    $http = weathermanHttp();
    $http->fake(fn () => Factory::response(['id' => '1', 'name' => 'n']));

    (new PendingNwsRequest(
        base: NwsURL::API,
        path: 'alerts/active',
        call_name: 'weatherman.alerts.active',
        hydrator: CoreArchSampleRecord::class,
        user_agent: 'test',
        http: $http,
    ))->with('severity', 'Moderate')->get();

    $http->assertSent(fn ($request) => str_contains($request->url(), 'severity=Moderate'));
});
