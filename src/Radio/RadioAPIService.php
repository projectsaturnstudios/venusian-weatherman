<?php

namespace ProjectSaturnStudios\Weatherman\Radio;

use ProjectSaturnStudios\Weatherman\Exceptions\NotYetSupportedException;

class RadioAPIService
{
    public function __construct()
    {
        throw NotYetSupportedException::forApi('NOAA Weather Radio');
    }
}
