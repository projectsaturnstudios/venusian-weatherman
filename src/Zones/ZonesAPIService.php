<?php

namespace ProjectSaturnStudios\Weatherman\Zones;

use ProjectSaturnStudios\Weatherman\Exceptions\NotYetSupportedException;

class ZonesAPIService
{
    public function __construct()
    {
        throw NotYetSupportedException::forApi('Zones');
    }
}
