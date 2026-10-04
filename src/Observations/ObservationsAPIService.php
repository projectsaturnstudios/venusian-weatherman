<?php

namespace ProjectSaturnStudios\Weatherman\Observations;

use InvalidArgumentException;
use ProjectSaturnStudios\Weatherman\Enums\NwsURL;
use ProjectSaturnStudios\Weatherman\Exceptions\NotYetSupportedException;
use ProjectSaturnStudios\Weatherman\NwsApiService;
use ProjectSaturnStudios\Weatherman\Observations\DataObjects\Observation;
use ProjectSaturnStudios\Weatherman\PendingNwsRequest;

class ObservationsAPIService extends NwsApiService
{
    /**
     * The latest observation at a station.
     * with('require_qc', true) asks NWS to require quality-controlled values.
     */
    public function latest(string $station_id, ?bool $require_qc = null): PendingNwsRequest
    {
        return $this->pending(
            base: NwsURL::API,
            path: 'stations/'.$this->identifier($station_id).'/observations/latest',
            call_name: 'weatherman.observations.latest',
            hydrator: fn (array $payload): Observation => Observation::fromArray($payload, $this->client),
            query: $this->query(['require_qc' => $require_qc]),
        );
    }

    public function list(string $station_id): never
    {
        throw NotYetSupportedException::forApi('Station observation history for '.$this->identifier($station_id));
    }

    public function atTime(string $station_id, string $time): never
    {
        $time = trim($time);

        if ($time === '') {
            throw new InvalidArgumentException('An observation time must be non-empty.');
        }

        throw NotYetSupportedException::forApi(
            'Station observation at '.$time.' for '.$this->identifier($station_id),
        );
    }

    protected function identifier(string $station_id): string
    {
        $station_id = strtoupper(trim($station_id));

        if (! preg_match('/^[A-Z0-9]{3,10}$/', $station_id)) {
            throw new InvalidArgumentException("Station id [{$station_id}] must be 3 to 10 letters or digits.");
        }

        return $station_id;
    }
}
