<?php

namespace ProjectSaturnStudios\Weatherman\Support;

use InvalidArgumentException;

final class Coordinates
{
    public static function pair(float $latitude, float $longitude): string
    {
        self::assertLatitude($latitude);
        self::assertLongitude($longitude);

        return self::place($latitude).','.self::place($longitude);
    }

    public static function assertLatitude(float $latitude): void
    {
        if ($latitude < -90.0 || $latitude > 90.0) {
            throw new InvalidArgumentException("Latitude must be between -90 and 90, got {$latitude}.");
        }
    }

    public static function assertLongitude(float $longitude): void
    {
        if ($longitude < -180.0 || $longitude > 180.0) {
            throw new InvalidArgumentException("Longitude must be between -180 and 180, got {$longitude}.");
        }
    }

    /**
     * api.weather.gov accepts at most four decimal places on a point.
     */
    public static function place(float $value): string
    {
        $formatted = number_format($value, 4, '.', '');
        $formatted = rtrim(rtrim($formatted, '0'), '.');

        return $formatted === '' || $formatted === '-' ? '0' : $formatted;
    }
}
