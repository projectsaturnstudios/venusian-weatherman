<?php

return [
    /*
    |--------------------------------------------------------------------------
    | User-Agent
    |--------------------------------------------------------------------------
    |
    | api.weather.gov refuses anonymous clients. Set NWS_USER_AGENT to a
    | string that identifies the application, with a contact URL or email.
    |
    */

    'user_agent' => env('NWS_USER_AGENT', 'projectsaturnstudios/venusian-weatherman 0.10.0 (https://projectsaturnstudios.com)'),
];
