<?php

namespace ProjectSaturnStudios\Weatherman\Support;

use InvalidArgumentException;
use ProjectSaturnStudios\Weatherman\Enums\ForecastOffice;

final class GridpointAddress
{
    public function __construct(
        public string $office,
        public int $x,
        public int $y,
    ) {}

    public static function make(ForecastOffice|string $office, int $x, int $y): self
    {
        if ($x < 0 || $y < 0) {
            throw new InvalidArgumentException("Grid x and y must be 0 or greater, got {$x},{$y}.");
        }

        return new self(self::office($office), $x, $y);
    }

    public function path(string $suffix = ''): string
    {
        $path = "gridpoints/{$this->office}/{$this->x},{$this->y}";

        if ($suffix === '') {
            return $path;
        }

        return $path.'/'.ltrim($suffix, '/');
    }

    protected static function office(ForecastOffice|string $office): string
    {
        if ($office instanceof ForecastOffice) {
            return $office->value;
        }

        $known = ForecastOffice::tryFrom(strtoupper(trim($office)));

        if (is_null($known)) {
            throw new InvalidArgumentException("Unknown NWS forecast office [{$office}].");
        }

        return $known->value;
    }
}
