<?php

namespace ProjectSaturnStudios\Weatherman\Enums;

enum ForecastFeatureFlag: string
{
    case FORECAST_TEMPERATURE_QV = 'forecast_temperature_qv';
    case FORECAST_WIND_SPEED_QV = 'forecast_wind_speed_qv';
}
