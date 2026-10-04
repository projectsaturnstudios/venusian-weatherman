<?php

namespace ProjectSaturnStudios\Weatherman\Contracts;

interface HydratesFromArray
{
    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): static;
}
