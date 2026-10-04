<?php

namespace ProjectSaturnStudios\Weatherman\Forecast;

use ProjectSaturnStudios\Weatherman\Enums\ForecastOffice;
use ProjectSaturnStudios\Weatherman\Enums\NwsURL;
use ProjectSaturnStudios\Weatherman\Forecast\DataObjects\Forecast;
use ProjectSaturnStudios\Weatherman\NwsApiService;
use ProjectSaturnStudios\Weatherman\PendingNwsRequest;
use ProjectSaturnStudios\Weatherman\Support\GridpointAddress;

class ForecastAPIService extends NwsApiService
{
    /**
     * The 12-hour narrative forecast for a gridpoint.
     * `with('units', ForecastUnits::SI)` switches the textual units.
     * `header('Feature-Flags', ForecastFeatureFlag::FORECAST_TEMPERATURE_QV)` asks for quantitative temperature.
     */
    public function grid(ForecastOffice|string $office, int $x, int $y): PendingNwsRequest
    {
        return $this->period($office, $x, $y, 'forecast', 'weatherman.forecast.grid');
    }

    /**
     * The hourly narrative forecast for a gridpoint.
     */
    public function hourly(ForecastOffice|string $office, int $x, int $y): PendingNwsRequest
    {
        return $this->period($office, $x, $y, 'forecast/hourly', 'weatherman.forecast.hourly');
    }

    protected function period(ForecastOffice|string $office, int $x, int $y, string $suffix, string $call_name): PendingNwsRequest
    {
        $address = GridpointAddress::make($office, $x, $y);

        return $this->pending(
            base: NwsURL::API,
            path: $address->path($suffix),
            call_name: $call_name,
            hydrator: Forecast::class,
        );
    }
}
