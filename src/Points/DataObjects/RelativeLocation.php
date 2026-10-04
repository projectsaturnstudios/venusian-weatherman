<?php

namespace ProjectSaturnStudios\Weatherman\Points\DataObjects;

use ProjectSaturnStudios\Weatherman\Contracts\HydratesFromArray;
use ProjectSaturnStudios\Weatherman\DataObjects\Geometry;
use ProjectSaturnStudios\Weatherman\DataObjects\QuantitativeValue;
use ProjectSaturnStudios\Weatherman\Support\HydratesNwsData;

final readonly class RelativeLocation implements HydratesFromArray
{
    use HydratesNwsData;

    public function __construct(
        public ?string $city,
        public ?string $state,
        public ?QuantitativeValue $distance,
        public ?QuantitativeValue $bearing,
        public ?Geometry $geometry,
    ) {}

    public static function fromArray(array $data): static
    {
        $properties = self::properties($data);

        return new self(
            city: self::optionalText($properties, 'city'),
            state: self::optionalText($properties, 'state'),
            distance: self::quantitative($properties['distance'] ?? null),
            bearing: self::quantitative($properties['bearing'] ?? null),
            geometry: self::geometry($data),
        );
    }
}
