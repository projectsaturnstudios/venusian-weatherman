<?php

namespace ProjectSaturnStudios\Weatherman\Alerts\DataObjects;

use ProjectSaturnStudios\Weatherman\Contracts\HydratesFromArray;
use ProjectSaturnStudios\Weatherman\NwsClient;
use ProjectSaturnStudios\Weatherman\PendingNwsRequest;
use ProjectSaturnStudios\Weatherman\Support\FollowsNwsLinks;
use ProjectSaturnStudios\Weatherman\Support\HydratesNwsData;
use Voyager\NutsAndBolts\Collection;

final readonly class AlertPage implements HydratesFromArray
{
    use FollowsNwsLinks;
    use HydratesNwsData;

    /**
     * @param  Collection<int, Alert>  $alerts
     */
    public function __construct(
        public ?string $title,
        public ?string $updated,
        public Collection $alerts,
        public ?string $next_url,
        protected ?NwsClient $client = null,
    ) {}

    public static function fromArray(array $data, ?NwsClient $client = null): static
    {
        $features = is_array($data['features'] ?? null) ? $data['features'] : [];
        $next = $data['pagination']['next'] ?? null;

        // An empty page is the end of the walk, even when pagination.next is set.
        if ($features === []) {
            $next = null;
        }

        return new self(
            title: self::optionalText($data, 'title'),
            updated: self::optionalText($data, 'updated'),
            alerts: Collection::make($features)->map(
                fn (mixed $feature): Alert => Alert::fromArray((array) $feature),
            ),
            next_url: is_string($next) && $next !== '' ? $next : null,
            client: $client,
        );
    }

    public function next(): ?PendingNwsRequest
    {
        if (is_null($this->next_url)) {
            return null;
        }

        $client = $this->nws();

        return $client->follow(
            $this->next_url,
            'weatherman.alerts.next',
            fn (array $payload): self => self::fromArray($payload, $client),
        );
    }
}
