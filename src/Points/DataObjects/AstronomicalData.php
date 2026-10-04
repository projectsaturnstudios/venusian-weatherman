<?php

namespace ProjectSaturnStudios\Weatherman\Points\DataObjects;

use ProjectSaturnStudios\Weatherman\Contracts\HydratesFromArray;
use ProjectSaturnStudios\Weatherman\Support\HydratesNwsData;

final readonly class AstronomicalData implements HydratesFromArray
{
    use HydratesNwsData;

    public function __construct(
        public ?string $sunrise,
        public ?string $sunset,
        public ?string $transit,
        public ?string $civil_twilight_begin,
        public ?string $civil_twilight_end,
        public ?string $nautical_twilight_begin,
        public ?string $nautical_twilight_end,
        public ?string $astronomical_twilight_begin,
        public ?string $astronomical_twilight_end,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            sunrise: self::optionalText($data, 'sunrise'),
            sunset: self::optionalText($data, 'sunset'),
            transit: self::optionalText($data, 'transit'),
            civil_twilight_begin: self::optionalText($data, 'civilTwilightBegin'),
            civil_twilight_end: self::optionalText($data, 'civilTwilightEnd'),
            nautical_twilight_begin: self::optionalText($data, 'nauticalTwilightBegin'),
            nautical_twilight_end: self::optionalText($data, 'nauticalTwilightEnd'),
            astronomical_twilight_begin: self::optionalText($data, 'astronomicalTwilightBegin'),
            astronomical_twilight_end: self::optionalText($data, 'astronomicalTwilightEnd'),
        );
    }
}
