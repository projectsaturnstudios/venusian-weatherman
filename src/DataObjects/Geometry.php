<?php

namespace ProjectSaturnStudios\Weatherman\DataObjects;

final readonly class Geometry
{
    /**
     * @param  array<int|string, mixed>  $coordinates
     */
    public function __construct(
        public ?string $type,
        public array $coordinates,
    ) {}

    /**
     * @param  array<string, mixed>  $document
     */
    public static function fromDocument(array $document): ?self
    {
        $geometry = $document['geometry'] ?? null;

        if (! is_array($geometry)) {
            return null;
        }

        return new self(
            type: isset($geometry['type']) ? (string) $geometry['type'] : null,
            coordinates: is_array($geometry['coordinates'] ?? null) ? $geometry['coordinates'] : [],
        );
    }

    public function longitude(): ?float
    {
        return $this->axis(0);
    }

    public function latitude(): ?float
    {
        return $this->axis(1);
    }

    protected function axis(int $index): ?float
    {
        if ($this->type !== 'Point' || ! array_is_list($this->coordinates) || ! isset($this->coordinates[$index]) || ! is_numeric($this->coordinates[$index])) {
            return null;
        }

        return (float) $this->coordinates[$index];
    }
}
