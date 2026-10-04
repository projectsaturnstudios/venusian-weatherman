<?php

namespace ProjectSaturnStudios\Weatherman\Alerts;

use InvalidArgumentException;
use ProjectSaturnStudios\Weatherman\Alerts\DataObjects\Alert;
use ProjectSaturnStudios\Weatherman\Alerts\DataObjects\AlertCount;
use ProjectSaturnStudios\Weatherman\Alerts\DataObjects\AlertPage;
use ProjectSaturnStudios\Weatherman\Enums\MarineRegion;
use ProjectSaturnStudios\Weatherman\Enums\NwsURL;
use ProjectSaturnStudios\Weatherman\NwsApiService;
use ProjectSaturnStudios\Weatherman\PendingNwsRequest;
use ProjectSaturnStudios\Weatherman\Support\Coordinates;
use ProjectSaturnStudios\Weatherman\Support\ZoneId;
use Voyager\NutsAndBolts\Collection;

class AlertsAPIService extends NwsApiService
{
    /**
     * Active alerts for the country. Add filters with with(): status, message_type,
     * severity, urgency, certainty, event, code, region_type.
     * status and message_type are lowercase on the wire; severity, urgency, and certainty stay title case.
     */
    public function active(): PendingNwsRequest
    {
        return $this->page('alerts/active', 'weatherman.alerts.active');
    }

    /**
     * The full alert collection, including alerts that are no longer active when start and end are set.
     */
    public function collection(): PendingNwsRequest
    {
        return $this->page('alerts', 'weatherman.alerts.collection');
    }

    public function at(float $latitude, float $longitude): PendingNwsRequest
    {
        return $this->active()->with('point', Coordinates::pair($latitude, $longitude));
    }

    public function area(string $area): PendingNwsRequest
    {
        $area = strtoupper(trim($area));

        if (! preg_match('/^[A-Z]{2}$/', $area)) {
            throw new InvalidArgumentException("Area code [{$area}] must be a two-letter state, territory, or marine area code.");
        }

        return $this->page('alerts/active/area/'.$area, 'weatherman.alerts.area');
    }

    public function zone(string $zone_id): PendingNwsRequest
    {
        return $this->page('alerts/active/zone/'.ZoneId::assert($zone_id), 'weatherman.alerts.zone');
    }

    public function region(MarineRegion $region): PendingNwsRequest
    {
        return $this->page('alerts/active/region/'.$region->value, 'weatherman.alerts.region');
    }

    public function find(string $id): PendingNwsRequest
    {
        $id = trim($id);

        if ($id === '' || str_contains($id, '/')) {
            throw new InvalidArgumentException('An alert id must be a non-empty path segment.');
        }

        return $this->pending(
            base: NwsURL::API,
            path: 'alerts/'.$id,
            call_name: 'weatherman.alerts.find',
            hydrator: Alert::class,
        );
    }

    public function count(): PendingNwsRequest
    {
        return $this->pending(
            base: NwsURL::API,
            path: 'alerts/active/count',
            call_name: 'weatherman.alerts.count',
            hydrator: AlertCount::class,
        );
    }

    public function types(): PendingNwsRequest
    {
        return $this->pending(
            base: NwsURL::API,
            path: 'alerts/types',
            call_name: 'weatherman.alerts.types',
            hydrator: function (mixed $payload): Collection {
                $types = is_array($payload) ? ($payload['eventTypes'] ?? []) : [];

                return Collection::make(is_array($types) ? $types : [])->map(
                    fn (mixed $type): string => (string) $type,
                );
            },
        );
    }

    protected function page(string $path, string $call_name): PendingNwsRequest
    {
        return $this->pending(
            base: NwsURL::API,
            path: $path,
            call_name: $call_name,
            hydrator: fn (array $payload): AlertPage => AlertPage::fromArray($payload, $this->client),
        );
    }
}
