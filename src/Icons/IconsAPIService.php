<?php

namespace ProjectSaturnStudios\Weatherman\Icons;

use ProjectSaturnStudios\Weatherman\Exceptions\NotYetSupportedException;

class IconsAPIService
{
    public function __construct()
    {
        throw NotYetSupportedException::forApi('Icons');
    }
}
