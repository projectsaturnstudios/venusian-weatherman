<?php

namespace ProjectSaturnStudios\Weatherman\Radar;

use ProjectSaturnStudios\Weatherman\Exceptions\NotYetSupportedException;

class RadarAPIService
{
    public function __construct()
    {
        throw NotYetSupportedException::forApi('Radar');
    }
}
