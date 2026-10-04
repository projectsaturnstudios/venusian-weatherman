<?php

namespace ProjectSaturnStudios\Weatherman\Exceptions;

class NotYetSupportedException extends WeathermanException
{
    public static function forApi(string $name): self
    {
        return new self("{$name} is not yet supported by Weatherman.");
    }
}
