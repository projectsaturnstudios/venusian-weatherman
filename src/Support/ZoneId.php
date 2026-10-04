<?php

namespace ProjectSaturnStudios\Weatherman\Support;

use InvalidArgumentException;

final class ZoneId
{
    public static function assert(string $zone_id): string
    {
        $zone_id = strtoupper(trim($zone_id));

        if (! preg_match(self::pattern(), $zone_id)) {
            throw new InvalidArgumentException("Zone id [{$zone_id}] is not an NWS UGC zone id.");
        }

        return $zone_id;
    }

    /**
     * UGC zone pattern from api.weather.gov OpenAPI 3.11.0 (NWSZoneID).
     */
    protected static function pattern(): string
    {
        return '/^(A[KLMNRSZ]|C[AOT]|D[CE]|F[LM]|G[AMU]|I[ADLN]|K[SY]|L[ACEHMOS]|M[ADEHINOPST]|N[CDEHJMVY]|O[HKR]|P[AHKMRSWZ]|S[CDL]|T[NX]|UT|V[AIT]|W[AIVY]|[HR]I)[CZ]\d{3}$/';
    }
}
