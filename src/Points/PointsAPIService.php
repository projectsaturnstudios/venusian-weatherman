<?php

namespace ProjectSaturnStudios\Weatherman\Points;

use ProjectSaturnStudios\Weatherman\Enums\NwsURL;
use ProjectSaturnStudios\Weatherman\NwsApiService;
use ProjectSaturnStudios\Weatherman\PendingNwsRequest;
use ProjectSaturnStudios\Weatherman\Points\DataObjects\Point;
use ProjectSaturnStudios\Weatherman\Support\Coordinates;

class PointsAPIService extends NwsApiService
{
    /**
     * The metadata document for a latitude,longitude. Its forecast, hourly,
     * and station links are the addresses NWS wants followed.
     */
    public function at(float $latitude, float $longitude): PendingNwsRequest
    {
        return $this->pending(
            base: NwsURL::API,
            path: 'points/'.Coordinates::pair($latitude, $longitude),
            call_name: 'weatherman.points.at',
            hydrator: fn (array $payload): Point => Point::fromArray($payload, $this->client),
        );
    }
}
