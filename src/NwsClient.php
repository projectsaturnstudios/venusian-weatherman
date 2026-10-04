<?php

namespace ProjectSaturnStudios\Weatherman;

use Closure;
use ProjectSaturnStudios\Weatherman\Alerts\AlertsAPIService;
use ProjectSaturnStudios\Weatherman\Aviation\AviationAPIService;
use ProjectSaturnStudios\Weatherman\Enums\NwsURL;
use ProjectSaturnStudios\Weatherman\Forecast\ForecastAPIService;
use ProjectSaturnStudios\Weatherman\Glossary\GlossaryAPIService;
use ProjectSaturnStudios\Weatherman\Gridpoints\GridpointAPIService;
use ProjectSaturnStudios\Weatherman\Icons\IconsAPIService;
use ProjectSaturnStudios\Weatherman\Observations\ObservationsAPIService;
use ProjectSaturnStudios\Weatherman\Offices\OfficesAPIService;
use ProjectSaturnStudios\Weatherman\Points\PointsAPIService;
use ProjectSaturnStudios\Weatherman\Products\ProductsAPIService;
use ProjectSaturnStudios\Weatherman\Radar\RadarAPIService;
use ProjectSaturnStudios\Weatherman\Radio\RadioAPIService;
use ProjectSaturnStudios\Weatherman\Stations\StationsAPIService;
use ProjectSaturnStudios\Weatherman\Tafs\TafsAPIService;
use ProjectSaturnStudios\Weatherman\Thumbnails\ThumbnailsAPIService;
use ProjectSaturnStudios\Weatherman\Zones\ZonesAPIService;
use Voyager\Http\Client\Factory;

class NwsClient
{
    public function __construct(
        protected ?string $user_agent = null,
        protected ?Factory $http = null,
    ) {}

    /**
     * @param  Closure(mixed):mixed|class-string|null  $hydrator
     * @param  array<string, mixed>  $query
     */
    public function pending(
        NwsURL $base,
        string $path,
        string $call_name,
        Closure|string|null $hydrator = null,
        array $query = [],
    ): PendingNwsRequest {
        return new PendingNwsRequest(
            base: $base,
            path: $path,
            call_name: $call_name,
            hydrator: $hydrator,
            query: $query,
            user_agent: $this->user_agent,
            http: $this->http,
        );
    }

    /**
     * @param  Closure(mixed):mixed|class-string|null  $hydrator
     */
    public function follow(string $url, string $call_name, Closure|string|null $hydrator): PendingNwsRequest
    {
        return PendingNwsRequest::absolute(
            url: $url,
            call_name: $call_name,
            hydrator: $hydrator,
            user_agent: $this->user_agent,
            http: $this->http,
        );
    }

    public function points(): PointsAPIService
    {
        return new PointsAPIService($this);
    }

    public function forecast(): ForecastAPIService
    {
        return new ForecastAPIService($this);
    }

    public function alerts(): AlertsAPIService
    {
        return new AlertsAPIService($this);
    }

    public function stations(): StationsAPIService
    {
        return new StationsAPIService($this);
    }

    public function observations(): ObservationsAPIService
    {
        return new ObservationsAPIService($this);
    }

    public function aviation(): AviationAPIService
    {
        return new AviationAPIService;
    }

    public function glossary(): GlossaryAPIService
    {
        return new GlossaryAPIService;
    }

    public function gridpoints(): GridpointAPIService
    {
        return new GridpointAPIService;
    }

    public function icons(): IconsAPIService
    {
        return new IconsAPIService;
    }

    public function thumbnails(): ThumbnailsAPIService
    {
        return new ThumbnailsAPIService;
    }

    public function offices(): OfficesAPIService
    {
        return new OfficesAPIService;
    }

    public function radar(): RadarAPIService
    {
        return new RadarAPIService;
    }

    public function radio(): RadioAPIService
    {
        return new RadioAPIService;
    }

    public function products(): ProductsAPIService
    {
        return new ProductsAPIService;
    }

    public function zones(): ZonesAPIService
    {
        return new ZonesAPIService;
    }

    public function tafs(): TafsAPIService
    {
        return new TafsAPIService;
    }
}
