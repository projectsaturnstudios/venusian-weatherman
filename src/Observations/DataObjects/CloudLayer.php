<?php

namespace ProjectSaturnStudios\Weatherman\Observations\DataObjects;

use ProjectSaturnStudios\Weatherman\Contracts\HydratesFromArray;
use ProjectSaturnStudios\Weatherman\DataObjects\QuantitativeValue;
use ProjectSaturnStudios\Weatherman\Support\HydratesNwsData;

final readonly class CloudLayer implements HydratesFromArray
{
    use HydratesNwsData;

    public function __construct(
        public ?QuantitativeValue $base,
        public ?string $amount,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            base: self::quantitative($data['base'] ?? null),
            amount: self::optionalText($data, 'amount'),
        );
    }
}
