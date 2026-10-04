<?php

namespace ProjectSaturnStudios\Weatherman;

use Closure;
use ProjectSaturnStudios\Weatherman\Enums\NwsURL;

class NwsApiService
{
    public function __construct(
        protected NwsClient $client,
    ) {}

    /**
     * @param  Closure(mixed):mixed|class-string|null  $hydrator  Shapes the decoded JSON for get() and async() alike.
     * @param  array<string, mixed>  $query
     */
    public function pending(
        NwsURL $base,
        string $path,
        string $call_name,
        Closure|string|null $hydrator = null,
        array $query = [],
    ): PendingNwsRequest {
        return $this->client->pending($base, $path, $call_name, $hydrator, $query);
    }

    /**
     * @param  array<string, mixed>  $pairs
     * @return array<string, mixed>
     */
    protected function query(array $pairs): array
    {
        $query = [];

        foreach ($pairs as $name => $value) {
            if (is_null($value)) {
                continue;
            }

            $query[$name] = $value;
        }

        return $query;
    }
}
