<?php

namespace ProjectSaturnStudios\Weatherman\Enums;

enum AlertUrgency: string
{
    case IMMEDIATE = 'Immediate';
    case EXPECTED = 'Expected';
    case FUTURE = 'Future';
    case PAST = 'Past';
    case UNKNOWN = 'Unknown';
}
