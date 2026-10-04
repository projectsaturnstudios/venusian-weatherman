<?php

namespace ProjectSaturnStudios\Weatherman\Tafs;

use ProjectSaturnStudios\Weatherman\Exceptions\NotYetSupportedException;

class TafsAPIService
{
    public function __construct()
    {
        throw NotYetSupportedException::forApi('TAFs');
    }
}
