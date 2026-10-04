<?php

namespace ProjectSaturnStudios\Weatherman\Points\DataObjects;

use ProjectSaturnStudios\Weatherman\Contracts\HydratesFromArray;
use ProjectSaturnStudios\Weatherman\Support\HydratesNwsData;

final readonly class WeatherRadio implements HydratesFromArray
{
    use HydratesNwsData;

    public function __construct(
        public ?string $transmitter,
        public ?string $same_code,
        public ?string $area_broadcast,
        public ?string $point_broadcast,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            transmitter: self::optionalText($data, 'transmitter'),
            same_code: self::optionalText($data, 'sameCode'),
            area_broadcast: self::optionalText($data, 'areaBroadcast'),
            point_broadcast: self::optionalText($data, 'pointBroadcast'),
        );
    }
}
