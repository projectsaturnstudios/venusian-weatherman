<?php

namespace ProjectSaturnStudios\Weatherman\Products;

use ProjectSaturnStudios\Weatherman\Exceptions\NotYetSupportedException;

class ProductsAPIService
{
    public function __construct()
    {
        throw NotYetSupportedException::forApi('Products');
    }
}
