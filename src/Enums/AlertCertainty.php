<?php

namespace ProjectSaturnStudios\Weatherman\Enums;

enum AlertCertainty: string
{
    case OBSERVED = 'Observed';
    case LIKELY = 'Likely';
    case POSSIBLE = 'Possible';
    case UNLIKELY = 'Unlikely';
    case UNKNOWN = 'Unknown';
}
