<?php

namespace ProjectSaturnStudios\Weatherman\Support;

use ProjectSaturnStudios\Weatherman\Contracts\HydratesFromArray;
use ProjectSaturnStudios\Weatherman\DataObjects\Geometry;
use ProjectSaturnStudios\Weatherman\DataObjects\QuantitativeValue;
use Voyager\NutsAndBolts\Collection;

trait HydratesNwsData
{
    protected static function text(array $data, string $key, string $default = ''): string
    {
        return array_key_exists($key, $data) && ! is_null($data[$key]) ? (string) $data[$key] : $default;
    }

    protected static function optionalText(array $data, string $key): ?string
    {
        if (! array_key_exists($key, $data) || is_null($data[$key])) {
            return null;
        }

        return (string) $data[$key];
    }

    protected static function optionalInt(array $data, string $key): ?int
    {
        if (! array_key_exists($key, $data) || is_null($data[$key]) || $data[$key] === '') {
            return null;
        }

        return (int) $data[$key];
    }

    protected static function optionalFloat(array $data, string $key): ?float
    {
        if (! array_key_exists($key, $data) || is_null($data[$key]) || $data[$key] === '') {
            return null;
        }

        return (float) $data[$key];
    }

    protected static function optionalBool(array $data, string $key): ?bool
    {
        if (! array_key_exists($key, $data) || is_null($data[$key])) {
            return null;
        }

        return (bool) $data[$key];
    }

    /**
     * GeoJSON features keep the document fields under properties.
     * A payload that is already the properties object is returned as-is.
     *
     * @param  array<string, mixed>  $document
     * @return array<string, mixed>
     */
    protected static function properties(array $document): array
    {
        if (isset($document['properties']) && is_array($document['properties'])) {
            return $document['properties'];
        }

        return $document;
    }

    /**
     * @param  array<string, mixed>  $document
     */
    protected static function geometry(array $document): ?Geometry
    {
        return Geometry::fromDocument($document);
    }

    protected static function quantitative(mixed $value): ?QuantitativeValue
    {
        if (! is_array($value)) {
            return null;
        }

        return QuantitativeValue::fromArray($value);
    }

    /**
     * @return Collection<int, string>
     */
    protected static function stringList(mixed $rows): Collection
    {
        if (is_string($rows) && $rows !== '') {
            return Collection::make([$rows]);
        }

        if (! is_array($rows)) {
            return Collection::make();
        }

        $values = [];

        foreach ($rows as $row) {
            if (is_null($row) || $row === '') {
                continue;
            }

            $values[] = (string) $row;
        }

        return Collection::make($values);
    }

    /**
     * @return array<string, list<string>>
     */
    protected static function stringMap(mixed $map): array
    {
        if (! is_array($map)) {
            return [];
        }

        $normalized = [];

        foreach ($map as $key => $value) {
            $normalized[(string) $key] = self::stringList($value)->values()->all();
        }

        return $normalized;
    }

    /**
     * @param  class-string<HydratesFromArray>  $dto
     */
    protected static function collectionOf(mixed $rows, string $dto): Collection
    {
        if (! is_array($rows)) {
            return Collection::make();
        }

        return Collection::make($rows)->map(
            fn (mixed $row) => $dto::fromArray((array) $row),
        );
    }
}
