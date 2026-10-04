<?php

namespace ProjectSaturnStudios\Weatherman\Stations\DataObjects;

use ProjectSaturnStudios\Weatherman\Contracts\HydratesFromArray;
use ProjectSaturnStudios\Weatherman\NwsClient;
use ProjectSaturnStudios\Weatherman\PendingNwsRequest;
use ProjectSaturnStudios\Weatherman\Support\FollowsNwsLinks;
use ProjectSaturnStudios\Weatherman\Support\HydratesNwsData;
use Voyager\NutsAndBolts\Collection;

final readonly class StationPage implements HydratesFromArray
{
    use FollowsNwsLinks;
    use HydratesNwsData;

    /**
     * @param  Collection<int, Station>  $stations
     * @param  Collection<int, string>  $observation_station_urls
     */
    public function __construct(
        public Collection $stations,
        public Collection $observation_station_urls,
        public ?string $next_url,
        protected ?NwsClient $client = null,
    ) {}

    public static function fromArray(array $data, ?NwsClient $client = null): static
    {
        $features = is_array($data['features'] ?? null) ? $data['features'] : [];
        $urls = self::stringList($data['observationStations'] ?? null);
        $next = $data['pagination']['next'] ?? null;

        // weather.gov keeps pagination.next on empty station pages, and each
        // empty page points at another empty page. A page with nothing on it
        // is the end of the walk.
        if ($features === [] && $urls->isEmpty()) {
            $next = null;
        }

        return new self(
            stations: Collection::make($features)->map(
                fn (mixed $feature): Station => Station::fromArray((array) $feature, $client),
            ),
            observation_station_urls: $urls,
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
            'weatherman.stations.next',
            fn (array $payload): self => self::fromArray($payload, $client),
        );
    }
}
