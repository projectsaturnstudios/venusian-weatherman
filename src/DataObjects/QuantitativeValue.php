<?php

namespace ProjectSaturnStudios\Weatherman\DataObjects;

use ProjectSaturnStudios\Weatherman\Contracts\HydratesFromArray;
use ProjectSaturnStudios\Weatherman\Enums\QualityControl;
use ProjectSaturnStudios\Weatherman\Support\HydratesNwsData;

final readonly class QuantitativeValue implements HydratesFromArray
{
    use HydratesNwsData;

    public function __construct(
        public ?float $value,
        public ?float $min_value,
        public ?float $max_value,
        public ?string $unit_code,
        public ?string $quality_control,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            value: self::optionalFloat($data, 'value'),
            min_value: self::optionalFloat($data, 'minValue'),
            max_value: self::optionalFloat($data, 'maxValue'),
            unit_code: self::optionalText($data, 'unitCode'),
            quality_control: self::optionalText($data, 'qualityControl'),
        );
    }

    public function knownQuality(): ?QualityControl
    {
        if (is_null($this->quality_control)) {
            return null;
        }

        return QualityControl::tryFrom($this->quality_control);
    }
}
