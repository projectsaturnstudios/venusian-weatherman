<?php

namespace ProjectSaturnStudios\Weatherman\Enums;

/**
 * Values accepted by the status query parameter. The document uses title case;
 * the query rejects it.
 */
enum AlertQueryStatus: string
{
    case ACTUAL = 'actual';
    case EXERCISE = 'exercise';
    case SYSTEM = 'system';
    case TEST = 'test';
    case DRAFT = 'draft';
}
