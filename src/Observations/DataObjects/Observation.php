<?php

namespace ProjectSaturnStudios\Weatherman\Observations\DataObjects;

use ProjectSaturnStudios\Weatherman\Contracts\HydratesFromArray;
use ProjectSaturnStudios\Weatherman\DataObjects\Geometry;
use ProjectSaturnStudios\Weatherman\DataObjects\QuantitativeValue;
use ProjectSaturnStudios\Weatherman\NwsClient;
use ProjectSaturnStudios\Weatherman\PendingNwsRequest;
use ProjectSaturnStudios\Weatherman\Stations\DataObjects\Station;
use ProjectSaturnStudios\Weatherman\Support\FollowsNwsLinks;
use ProjectSaturnStudios\Weatherman\Support\HydratesNwsData;
use Voyager\NutsAndBolts\Collection;

final readonly class Observation implements HydratesFromArray
{
    use FollowsNwsLinks;
    use HydratesNwsData;

    /**
     * @param  Collection<int, PresentWeather>  $present_weather
     * @param  Collection<int, CloudLayer>  $cloud_layers
     */
    public function __construct(
        public ?string $id,
        public ?string $station_url,
        public ?string $station_id,
        public ?string $station_name,
        public ?string $timestamp,
        public ?string $raw_message,
        public ?string $text_description,
        public ?string $icon,
        public Collection $present_weather,
        public Collection $cloud_layers,
        public ?QuantitativeValue $elevation,
        public ?QuantitativeValue $temperature,
        public ?QuantitativeValue $dewpoint,
        public ?QuantitativeValue $wind_direction,
        public ?QuantitativeValue $wind_speed,
        public ?QuantitativeValue $wind_gust,
        public ?QuantitativeValue $barometric_pressure,
        public ?QuantitativeValue $sea_level_pressure,
        public ?QuantitativeValue $visibility,
        public ?QuantitativeValue $max_temperature_last_24_hours,
        public ?QuantitativeValue $min_temperature_last_24_hours,
        public ?QuantitativeValue $precipitation_last_hour,
        public ?QuantitativeValue $precipitation_last_3_hours,
        public ?QuantitativeValue $precipitation_last_6_hours,
        public ?QuantitativeValue $relative_humidity,
        public ?QuantitativeValue $wind_chill,
        public ?QuantitativeValue $heat_index,
        public ?Geometry $geometry,
        protected ?NwsClient $client = null,
    ) {}

    public static function fromArray(array $data, ?NwsClient $client = null): static
    {
        $properties = self::properties($data);

        return new self(
            id: self::optionalText($properties, '@id'),
            station_url: self::optionalText($properties, 'station'),
            station_id: self::optionalText($properties, 'stationId'),
            station_name: self::optionalText($properties, 'stationName'),
            timestamp: self::optionalText($properties, 'timestamp'),
            raw_message: self::optionalText($properties, 'rawMessage'),
            text_description: self::optionalText($properties, 'textDescription'),
            icon: self::optionalText($properties, 'icon'),
            present_weather: self::collectionOf($properties['presentWeather'] ?? [], PresentWeather::class),
            cloud_layers: self::collectionOf($properties['cloudLayers'] ?? [], CloudLayer::class),
            elevation: self::quantitative($properties['elevation'] ?? null),
            temperature: self::quantitative($properties['temperature'] ?? null),
            dewpoint: self::quantitative($properties['dewpoint'] ?? null),
            wind_direction: self::quantitative($properties['windDirection'] ?? null),
            wind_speed: self::quantitative($properties['windSpeed'] ?? null),
            wind_gust: self::quantitative($properties['windGust'] ?? null),
            barometric_pressure: self::quantitative($properties['barometricPressure'] ?? null),
            sea_level_pressure: self::quantitative($properties['seaLevelPressure'] ?? null),
            visibility: self::quantitative($properties['visibility'] ?? null),
            max_temperature_last_24_hours: self::quantitative($properties['maxTemperatureLast24Hours'] ?? null),
            min_temperature_last_24_hours: self::quantitative($properties['minTemperatureLast24Hours'] ?? null),
            precipitation_last_hour: self::quantitative($properties['precipitationLastHour'] ?? null),
            precipitation_last_3_hours: self::quantitative($properties['precipitationLast3Hours'] ?? null),
            precipitation_last_6_hours: self::quantitative($properties['precipitationLast6Hours'] ?? null),
            relative_humidity: self::quantitative($properties['relativeHumidity'] ?? null),
            wind_chill: self::quantitative($properties['windChill'] ?? null),
            heat_index: self::quantitative($properties['heatIndex'] ?? null),
            geometry: self::geometry($data),
            client: $client,
        );
    }

    public function station(): PendingNwsRequest
    {
        $client = $this->nws();

        return $client->follow(
            $this->link('station', $this->station_url),
            'weatherman.observations.station',
            fn (array $payload): Station => Station::fromArray($payload, $client),
        );
    }
}
