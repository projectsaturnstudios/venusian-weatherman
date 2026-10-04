<?php

namespace ProjectSaturnStudios\Weatherman\Gridpoints;

use ProjectSaturnStudios\Weatherman\Exceptions\NotYetSupportedException;

class GridpointAPIService
{
    public function __construct()
    {
        throw NotYetSupportedException::forApi('Gridpoint data');
    }
}
