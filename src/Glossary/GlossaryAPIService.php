<?php

namespace ProjectSaturnStudios\Weatherman\Glossary;

use ProjectSaturnStudios\Weatherman\Exceptions\NotYetSupportedException;

class GlossaryAPIService
{
    public function __construct()
    {
        throw NotYetSupportedException::forApi('Glossary');
    }
}
