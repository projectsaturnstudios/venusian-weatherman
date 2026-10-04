<?php

namespace ProjectSaturnStudios\Weatherman\Stations\DataObjects;

use ProjectSaturnStudios\Weatherman\Contracts\HydratesFromArray;
use ProjectSaturnStudios\Weatherman\DataObjects\Geometry;
use ProjectSaturnStudios\Weatherman\DataObjects\QuantitativeValue;
use ProjectSaturnStudios\Weatherman\Exceptions\WeathermanException;
use ProjectSaturnStudios\Weatherman\NwsClient;
use ProjectSaturnStudios\Weatherman\PendingNwsRequest;
use ProjectSaturnStudios\Weatherman\Support\FollowsNwsLinks;
use ProjectSaturnStudios\Weatherman\Support\HydratesNwsData;

final readonly class Station implements HydratesFromArray
{
    use FollowsNwsLinks;
    use HydratesNwsData;

    public function __construct(
        public ?string $id,
        public ?string $station_identifier,
        public ?string $name,
        public ?string $time_zone,
        public ?string $provider,
        public ?string $sub_provider,
        public ?QuantitativeValue $elevation,
        public ?string $forecast_zone,
        public ?string $county,
        public ?string $fire_weather_zone,
        public ?QuantitativeValue $distance,
        public ?QuantitativeValue $bearing,
        public ?Geometry $geometry,
        protected ?NwsClient $client = null,
    ) {}

    public static function fromArray(array $data, ?NwsClient $client = null): static
    {
        $properties = self::properties($data);

        return new self(
            id: self::optionalText($properties, '@id'),
            station_identifier: self::optionalText($properties, 'stationIdentifier'),
            name: self::optionalText($properties, 'name'),
            time_zone: self::optionalText($properties, 'timeZone'),
            provider: self::optionalText($properties, 'provider'),
            sub_provider: self::optionalText($properties, 'subProvider'),
            elevation: self::quantitative($properties['elevation'] ?? null),
            forecast_zone: self::optionalText($properties, 'forecast'),
            county: self::optionalText($properties, 'county'),
            fire_weather_zone: self::optionalText($properties, 'fireWeatherZone'),
            distance: self::quantitative($properties['distance'] ?? null),
            bearing: self::quantitative($properties['bearing'] ?? null),
            geometry: self::geometry($data),
            client: $client,
        );
    }

    public function latest(): PendingNwsRequest
    {
        if (is_null($this->station_identifier) || $this->station_identifier === '') {
            throw WeathermanException::missingLink('station identifier');
        }

        $client = $this->nws();

        return $client->observations()->latest($this->station_identifier);
    }
}
