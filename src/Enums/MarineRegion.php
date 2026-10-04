<?php

namespace ProjectSaturnStudios\Weatherman\Enums;

enum MarineRegion: string
{
    /** Alaska waters. */
    case AL = 'AL';

    /** Atlantic Ocean. */
    case AT = 'AT';

    /** Great Lakes. */
    case GL = 'GL';

    /** Gulf of Mexico. */
    case GM = 'GM';

    /** Eastern Pacific Ocean and the U.S. West Coast. */
    case PA = 'PA';

    /** Central and Western Pacific. */
    case PI = 'PI';
}
