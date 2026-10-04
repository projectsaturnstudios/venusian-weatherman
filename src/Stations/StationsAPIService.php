<?php

namespace ProjectSaturnStudios\Weatherman\Stations;

use InvalidArgumentException;
use ProjectSaturnStudios\Weatherman\Enums\ForecastOffice;
use ProjectSaturnStudios\Weatherman\Enums\NwsURL;
use ProjectSaturnStudios\Weatherman\NwsApiService;
use ProjectSaturnStudios\Weatherman\PendingNwsRequest;
use ProjectSaturnStudios\Weatherman\Stations\DataObjects\Station;
use ProjectSaturnStudios\Weatherman\Stations\DataObjects\StationPage;
use ProjectSaturnStudios\Weatherman\Support\GridpointAddress;

class StationsAPIService extends NwsApiService
{
    /**
     * Observation stations. Filter with with('state', 'VA'), with('id', ...), with('limit', ...), or with('cursor', ...).
     * limit on the wire is 1 to 500.
     */
    public function index(?string $state = null, ?int $limit = null): PendingNwsRequest
    {
        if (! is_null($limit) && ($limit < 1 || $limit > 500)) {
            throw new InvalidArgumentException("An NWS page limit is 1 to 500, got {$limit}.");
        }

        $state = is_null($state) ? null : strtoupper(trim($state));

        if (! is_null($state) && ! preg_match('/^[A-Z]{2}$/', $state)) {
            throw new InvalidArgumentException("State [{$state}] must be a two-letter code.");
        }

        return $this->page('stations', 'weatherman.stations.index', array_filter([
            'state' => $state,
            'limit' => $limit,
        ], fn (mixed $value): bool => ! is_null($value)));
    }

    public function find(string $station_id): PendingNwsRequest
    {
        return $this->pending(
            base: NwsURL::API,
            path: 'stations/'.$this->identifier($station_id),
            call_name: 'weatherman.stations.find',
            hydrator: fn (array $payload): Station => Station::fromArray($payload, $this->client),
        );
    }

    /**
     * Stations for a gridpoint. This is the document observationStations on a point links to.
     * /points/{lat},{lon}/stations answers 301 and is not requested here.
     */
    public function grid(ForecastOffice|string $office, int $x, int $y): PendingNwsRequest
    {
        $address = GridpointAddress::make($office, $x, $y);

        return $this->page($address->path('stations'), 'weatherman.stations.grid');
    }

    /**
     * @param  array<string, mixed>  $query
     */
    protected function page(string $path, string $call_name, array $query = []): PendingNwsRequest
    {
        return $this->pending(
            base: NwsURL::API,
            path: $path,
            call_name: $call_name,
            hydrator: fn (array $payload): StationPage => StationPage::fromArray($payload, $this->client),
            query: $query,
        );
    }

    protected function identifier(string $station_id): string
    {
        $station_id = strtoupper(trim($station_id));

        if (! preg_match('/^[A-Z0-9]{3,10}$/', $station_id)) {
            throw new InvalidArgumentException("Station id [{$station_id}] must be 3 to 10 letters or digits.");
        }

        return $station_id;
    }
}
