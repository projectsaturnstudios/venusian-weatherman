<?php

namespace ProjectSaturnStudios\Weatherman\Aviation;

use ProjectSaturnStudios\Weatherman\Exceptions\NotYetSupportedException;

class AviationAPIService
{
    public function __construct()
    {
        throw NotYetSupportedException::forApi('Aviation');
    }
}
