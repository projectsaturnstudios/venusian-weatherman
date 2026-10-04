<?php

namespace ProjectSaturnStudios\Weatherman\Observations\DataObjects;

use ProjectSaturnStudios\Weatherman\Contracts\HydratesFromArray;
use ProjectSaturnStudios\Weatherman\Support\HydratesNwsData;

final readonly class PresentWeather implements HydratesFromArray
{
    use HydratesNwsData;

    public function __construct(
        public ?string $intensity,
        public ?string $modifier,
        public ?string $weather,
        public ?string $raw_string,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            intensity: self::optionalText($data, 'intensity'),
            modifier: self::optionalText($data, 'modifier'),
            weather: self::optionalText($data, 'weather'),
            raw_string: self::optionalText($data, 'rawString'),
        );
    }
}
