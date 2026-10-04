<?php

use ProjectSaturnStudios\Weatherman\NwsClient;

if (! function_exists('nws')) {
    /**
     * The National Weather Service client bound as 'nws'.
     */
    function nws(): NwsClient
    {
        return app('nws');
    }
}
