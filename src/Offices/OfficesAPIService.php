<?php

namespace ProjectSaturnStudios\Weatherman\Offices;

use ProjectSaturnStudios\Weatherman\Exceptions\NotYetSupportedException;

class OfficesAPIService
{
    public function __construct()
    {
        throw NotYetSupportedException::forApi('Offices');
    }
}
