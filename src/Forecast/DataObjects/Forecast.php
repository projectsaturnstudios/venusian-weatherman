<?php

namespace ProjectSaturnStudios\Weatherman\Forecast\DataObjects;

use ProjectSaturnStudios\Weatherman\Contracts\HydratesFromArray;
use ProjectSaturnStudios\Weatherman\DataObjects\Geometry;
use ProjectSaturnStudios\Weatherman\DataObjects\QuantitativeValue;
use ProjectSaturnStudios\Weatherman\Support\HydratesNwsData;
use Voyager\NutsAndBolts\Collection;

final readonly class Forecast implements HydratesFromArray
{
    use HydratesNwsData;

    /**
     * @param  Collection<int, ForecastPeriod>  $periods
     */
    public function __construct(
        public ?string $units,
        public ?string $generated_at,
        public ?string $update_time,
        public ?string $valid_times,
        public ?string $forecast_generator,
        public ?QuantitativeValue $elevation,
        public Collection $periods,
        public ?Geometry $geometry,
    ) {}

    public static function fromArray(array $data): static
    {
        $properties = self::properties($data);

        return new self(
            units: self::optionalText($properties, 'units'),
            generated_at: self::optionalText($properties, 'generatedAt'),
            update_time: self::optionalText($properties, 'updateTime'),
            valid_times: self::optionalText($properties, 'validTimes'),
            forecast_generator: self::optionalText($properties, 'forecastGenerator'),
            elevation: self::quantitative($properties['elevation'] ?? null),
            periods: self::collectionOf($properties['periods'] ?? [], ForecastPeriod::class),
            geometry: self::geometry($data),
        );
    }
}
