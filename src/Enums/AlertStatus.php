<?php

namespace ProjectSaturnStudios\Weatherman\Enums;

enum AlertStatus: string
{
    case ACTUAL = 'Actual';
    case EXERCISE = 'Exercise';
    case SYSTEM = 'System';
    case TEST = 'Test';
    case DRAFT = 'Draft';
}
