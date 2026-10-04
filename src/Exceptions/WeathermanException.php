<?php

namespace ProjectSaturnStudios\Weatherman\Exceptions;

use RuntimeException;

class WeathermanException extends RuntimeException
{
    /**
     * The HTTP status of the failed response; null when no response was involved.
     */
    protected ?int $status = null;

    public static function loopNotBound(): self
    {
        return new self(
            'No event loop is bound to the Http client. async() needs the Voyager loop; call get() for a blocking request.',
        );
    }

    public static function httpClientUnavailable(): self
    {
        return new self(
            'The Voyager Http client is not available. Bind Voyager\\Http\\Client\\Factory as \'http\' or pass one to NwsClient.',
        );
    }

    public static function userAgentRequired(): self
    {
        return new self(
            'api.weather.gov requires a User-Agent. Set nws.user_agent (NWS_USER_AGENT) or pass one to NwsClient.',
        );
    }

    public static function followUnavailable(): self
    {
        return new self(
            'This document was hydrated without an NWS client, so it cannot follow links. Load it through nws(), or call the service method that builds the URL.',
        );
    }

    public static function missingLink(string $name): self
    {
        return new self("This document has no {$name} link to follow.");
    }

    public static function requestFailed(int $status, string $url, string $body): self
    {
        $exception = new self("National Weather Service request failed ({$status}) for {$url}: {$body}");
        $exception->status = $status;

        return $exception;
    }

    /**
     * The HTTP status of the failed response, or null when no response was involved.
     */
    public function status(): ?int
    {
        return $this->status;
    }

    public static function invalidHydrator(mixed $hydrator): self
    {
        $label = is_string($hydrator) ? $hydrator : get_debug_type($hydrator);

        return new self("PendingNwsRequest hydrator [{$label}] must be a DTO class with fromArray() or a Closure.");
    }

    public static function invalidPayload(string $url): self
    {
        return new self("National Weather Service response for {$url} was not a JSON object or list.");
    }
}
