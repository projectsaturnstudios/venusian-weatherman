<?php

namespace ProjectSaturnStudios\Weatherman\Thumbnails;

use ProjectSaturnStudios\Weatherman\Exceptions\NotYetSupportedException;

class ThumbnailsAPIService
{
    public function __construct()
    {
        throw NotYetSupportedException::forApi('Satellite thumbnails');
    }
}
