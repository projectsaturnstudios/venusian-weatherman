<?php

namespace ProjectSaturnStudios\Weatherman\Alerts\DataObjects;

use ProjectSaturnStudios\Weatherman\Contracts\HydratesFromArray;
use ProjectSaturnStudios\Weatherman\Support\HydratesNwsData;

final readonly class AlertCount implements HydratesFromArray
{
    use HydratesNwsData;

    /**
     * @param  array<string, int>  $regions
     * @param  array<string, int>  $areas
     * @param  array<string, int>  $zones
     */
    public function __construct(
        public int $total,
        public int $land,
        public int $marine,
        public array $regions,
        public array $areas,
        public array $zones,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            total: (int) ($data['total'] ?? 0),
            land: (int) ($data['land'] ?? 0),
            marine: (int) ($data['marine'] ?? 0),
            regions: self::counts($data['regions'] ?? null),
            areas: self::counts($data['areas'] ?? null),
            zones: self::counts($data['zones'] ?? null),
        );
    }

    /**
     * @return array<string, int>
     */
    protected static function counts(mixed $map): array
    {
        if (! is_array($map)) {
            return [];
        }

        $counts = [];

        foreach ($map as $key => $value) {
            $counts[(string) $key] = (int) $value;
        }

        return $counts;
    }
}
