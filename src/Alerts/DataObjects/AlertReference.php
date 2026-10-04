<?php

namespace ProjectSaturnStudios\Weatherman\Alerts\DataObjects;

use ProjectSaturnStudios\Weatherman\Contracts\HydratesFromArray;
use ProjectSaturnStudios\Weatherman\Support\HydratesNwsData;

final readonly class AlertReference implements HydratesFromArray
{
    use HydratesNwsData;

    public function __construct(
        public ?string $id,
        public ?string $identifier,
        public ?string $sender,
        public ?string $sent,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            id: self::optionalText($data, '@id'),
            identifier: self::optionalText($data, 'identifier'),
            sender: self::optionalText($data, 'sender'),
            sent: self::optionalText($data, 'sent'),
        );
    }
}
