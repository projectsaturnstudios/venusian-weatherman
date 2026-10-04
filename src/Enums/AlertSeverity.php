<?php

namespace ProjectSaturnStudios\Weatherman\Enums;

enum AlertSeverity: string
{
    case EXTREME = 'Extreme';
    case SEVERE = 'Severe';
    case MODERATE = 'Moderate';
    case MINOR = 'Minor';
    case UNKNOWN = 'Unknown';
}
