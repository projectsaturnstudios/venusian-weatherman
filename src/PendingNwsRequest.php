<?php

namespace ProjectSaturnStudios\Weatherman;

use Closure;
use ProjectSaturnStudios\Weatherman\Enums\AlertMessageType;
use ProjectSaturnStudios\Weatherman\Enums\AlertStatus;
use ProjectSaturnStudios\Weatherman\Enums\NwsURL;
use ProjectSaturnStudios\Weatherman\Exceptions\WeathermanException;
use Voyager\Contracts\IOPools\Promise;
use Voyager\Http\Client\Factory;
use Voyager\Http\Client\Response;
use Voyager\NutsAndBolts\Collection;

class PendingNwsRequest
{
    /**
     * One hydrator serves both lanes: get() blocks and answers DTOs;
     * async() rides the event loop and answers a promise of the same DTOs.
     *
     * @param  array<string, mixed>  $query
     * @param  array<string, mixed>  $headers
     * @param  Closure(mixed):mixed|class-string|null  $hydrator
     */
    public function __construct(
        protected ?NwsURL $base,
        protected string $path,
        protected string $call_name,
        protected Closure|string|null $hydrator = null,
        protected array $query = [],
        protected array $headers = [],
        protected ?string $user_agent = null,
        protected ?Factory $http = null,
        protected ?string $absolute = null,
    ) {}

    /**
     * @param  Closure(mixed):mixed|class-string|null  $hydrator
     */
    public static function absolute(
        string $url,
        string $call_name,
        Closure|string|null $hydrator,
        ?string $user_agent,
        ?Factory $http,
    ): self {
        return new self(
            base: null,
            path: '',
            call_name: $call_name,
            hydrator: $hydrator,
            user_agent: $user_agent,
            http: $http,
            absolute: $url,
        );
    }

    public function with(string $name, mixed $value): static
    {
        $copy = clone $this;

        if (is_null($value)) {
            unset($copy->query[$name]);
        } else {
            $copy->query[$name] = $value;
        }

        return $copy;
    }

    public function header(string $name, mixed $value): static
    {
        $copy = clone $this;

        if (is_null($value)) {
            unset($copy->headers[$name]);
        } else {
            $copy->headers[$name] = $value;
        }

        return $copy;
    }

    public function __call(string $name, array $arguments): static
    {
        return $this->with($name, $arguments[0] ?? null);
    }

    public function get(): mixed
    {
        return $this->resolve($this->sender()->get($this->url()));
    }

    /**
     * Send on the event loop. The promise fulfils with what get() would
     * have returned and rejects with what get() would have thrown.
     */
    public function async(): Promise
    {
        $http = $this->httpFactory();

        if (is_null($http->loop())) {
            throw WeathermanException::loopNotBound();
        }

        return $this->sender()->async()->get($this->url())
            ->then(fn (Response $response): mixed => $this->resolve($response));
    }

    protected function resolve(Response $response): mixed
    {
        if (! $response->successful()) {
            throw WeathermanException::requestFailed(
                status: $response->status(),
                url: $this->url(),
                body: $response->body(),
            );
        }

        return $this->hydrate($response->json());
    }

    public function url(): string
    {
        $base = $this->absolute;

        if (is_null($base)) {
            $path = ltrim($this->path, '/');
            $base = $path === ''
                ? $this->base->value
                : rtrim($this->base->value, '/').'/'.$path;
        }

        $query = $this->encodedQuery();

        if ($query === '') {
            return $base;
        }

        return $base.(str_contains($base, '?') ? '&' : '?').$query;
    }

    /**
     * @return array<string, string>
     */
    public function query(): array
    {
        $query = [];

        foreach ($this->query as $name => $value) {
            if (is_null($value)) {
                continue;
            }

            $query[$name] = $this->queryValue($value);
        }

        return $query;
    }

    public function callName(): string
    {
        return $this->call_name;
    }

    /**
     * @return array<string, string>
     */
    public function headers(): array
    {
        $headers = [
            'User-Agent' => $this->userAgent(),
            'Accept' => 'application/geo+json',
        ];

        foreach ($this->headers as $name => $value) {
            $headers[$name] = $this->queryValue($value);
        }

        return $headers;
    }

    protected function encodedQuery(): string
    {
        $query = $this->query();

        if ($query === []) {
            return '';
        }

        return http_build_query($query);
    }

    protected function queryValue(mixed $value): string
    {
        if ($value instanceof AlertStatus || $value instanceof AlertMessageType) {
            return strtolower($value->value);
        }

        if ($value instanceof \BackedEnum) {
            return (string) $value->value;
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_array($value)) {
            $items = [];

            foreach ($value as $item) {
                $items[] = $this->queryValue($item);
            }

            return implode(',', $items);
        }

        return (string) $value;
    }

    protected function userAgent(): string
    {
        $agent = $this->user_agent;

        if (is_null($agent) || trim($agent) === '') {
            $agent = $this->configuredUserAgent();
        }

        if (is_null($agent) || trim($agent) === '') {
            throw WeathermanException::userAgentRequired();
        }

        return trim($agent);
    }

    protected function configuredUserAgent(): ?string
    {
        if (! function_exists('app') || ! app()->bound('config')) {
            return null;
        }

        $agent = app('config')->get('nws.user_agent');

        if (is_null($agent) || $agent === '') {
            return null;
        }

        return (string) $agent;
    }

    protected function sender(): mixed
    {
        return $this->httpFactory()->withHeaders($this->headers());
    }

    protected function httpFactory(): Factory
    {
        if (! is_null($this->http)) {
            return $this->http;
        }

        if (function_exists('app') && app()->bound('http')) {
            return app('http');
        }

        throw WeathermanException::httpClientUnavailable();
    }

    protected function hydrate(mixed $payload): mixed
    {
        if (! is_array($payload)) {
            throw WeathermanException::invalidPayload($this->url());
        }

        if ($this->hydrator instanceof Closure) {
            return ($this->hydrator)($payload);
        }

        $dto = $this->hydrator;

        if (! is_string($dto) || ! class_exists($dto) || ! method_exists($dto, 'fromArray')) {
            throw WeathermanException::invalidHydrator($dto);
        }

        if (array_is_list($payload)) {
            return Collection::make($payload)->map(
                fn (mixed $row) => $dto::fromArray((array) $row),
            );
        }

        return $dto::fromArray($payload);
    }
}
