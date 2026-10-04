<?php

namespace ProjectSaturnStudios\Weatherman\Points\DataObjects;

use ProjectSaturnStudios\Weatherman\Contracts\HydratesFromArray;
use ProjectSaturnStudios\Weatherman\DataObjects\Geometry;
use ProjectSaturnStudios\Weatherman\Enums\PointKind;
use ProjectSaturnStudios\Weatherman\Forecast\DataObjects\Forecast;
use ProjectSaturnStudios\Weatherman\NwsClient;
use ProjectSaturnStudios\Weatherman\PendingNwsRequest;
use ProjectSaturnStudios\Weatherman\Stations\DataObjects\StationPage;
use ProjectSaturnStudios\Weatherman\Support\FollowsNwsLinks;
use ProjectSaturnStudios\Weatherman\Support\HydratesNwsData;

final readonly class Point implements HydratesFromArray
{
    use FollowsNwsLinks;
    use HydratesNwsData;

    public function __construct(
        public ?string $id,
        public ?string $kind,
        public ?string $cwa,
        public ?string $forecast_office,
        public ?string $grid_id,
        public ?int $grid_x,
        public ?int $grid_y,
        public ?string $forecast_url,
        public ?string $hourly_url,
        public ?string $grid_url,
        public ?string $stations_url,
        public ?string $forecast_zone,
        public ?string $county,
        public ?string $fire_weather_zone,
        public ?string $time_zone,
        public ?string $radar_station,
        public ?RelativeLocation $relative_location,
        public ?AstronomicalData $astronomical_data,
        public ?WeatherRadio $radio,
        public ?Geometry $geometry,
        protected ?NwsClient $client = null,
    ) {}

    public static function fromArray(array $data, ?NwsClient $client = null): static
    {
        $properties = self::properties($data);
        $relative = $properties['relativeLocation'] ?? null;
        $astronomy = $properties['astronomicalData'] ?? null;
        $radio = $properties['nwr'] ?? null;

        return new self(
            id: self::optionalText($properties, '@id'),
            kind: self::optionalText($properties, 'type'),
            cwa: self::optionalText($properties, 'cwa'),
            forecast_office: self::optionalText($properties, 'forecastOffice'),
            grid_id: self::optionalText($properties, 'gridId'),
            grid_x: self::optionalInt($properties, 'gridX'),
            grid_y: self::optionalInt($properties, 'gridY'),
            forecast_url: self::optionalText($properties, 'forecast'),
            hourly_url: self::optionalText($properties, 'forecastHourly'),
            grid_url: self::optionalText($properties, 'forecastGridData'),
            stations_url: self::optionalText($properties, 'observationStations'),
            forecast_zone: self::optionalText($properties, 'forecastZone'),
            county: self::optionalText($properties, 'county'),
            fire_weather_zone: self::optionalText($properties, 'fireWeatherZone'),
            time_zone: self::optionalText($properties, 'timeZone'),
            radar_station: self::optionalText($properties, 'radarStation'),
            relative_location: is_array($relative) ? RelativeLocation::fromArray($relative) : null,
            astronomical_data: is_array($astronomy) ? AstronomicalData::fromArray($astronomy) : null,
            radio: is_array($radio) ? WeatherRadio::fromArray($radio) : null,
            geometry: self::geometry($data),
            client: $client,
        );
    }

    public function knownKind(): ?PointKind
    {
        if (is_null($this->kind)) {
            return null;
        }

        return PointKind::tryFrom($this->kind);
    }

    public function forecast(): PendingNwsRequest
    {
        return $this->nws()->follow($this->link('forecast', $this->forecast_url), 'weatherman.points.forecast', Forecast::class);
    }

    public function hourly(): PendingNwsRequest
    {
        return $this->nws()->follow($this->link('hourly forecast', $this->hourly_url), 'weatherman.points.hourly', Forecast::class);
    }

    public function stations(): PendingNwsRequest
    {
        $client = $this->nws();

        return $client->follow(
            $this->link('observation stations', $this->stations_url),
            'weatherman.points.stations',
            fn (array $payload): StationPage => StationPage::fromArray($payload, $client),
        );
    }
}
