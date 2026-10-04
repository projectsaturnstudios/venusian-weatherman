---
type: API Family
title: Forecast
description: 12-hour and hourly narrative forecasts for an NWS gridpoint.
tags:
  - forecast
  - nws
status: draft
generated:
  by: grok-4.7/2026-10-04
  at: '2026-10-04T03:45:00Z'
sources:
  - id: service
    resource: src/Forecast/ForecastAPIService.php
    title: ForecastAPIService
  - id: period
    resource: src/Forecast/DataObjects/ForecastPeriod.php
    title: ForecastPeriod
  - id: openapi
    resource: https://api.weather.gov/openapi.json
    title: weather.gov API OpenAPI 3.11.0
---

# Overview

`forecast()->grid($office, $x, $y)` requests `GET /gridpoints/{wfo}/{x},{y}/forecast`. `hourly()` requests the `/forecast/hourly` sibling. `$office` is a `ForecastOffice` case or one of those three-letter ids. A negative axis or an unknown office throws `InvalidArgumentException`.[^service][^openapi]

The raw quantitative grid, `GET /gridpoints/{wfo}/{x},{y}`, is the deferred [gridpoint data](/deferred-apis.md) accessor. It is not this family.

# Units and feature flags

`with('units', ForecastUnits::US)` or `ForecastUnits::SI` sets the textual units. The default on the service is US, which is also the API default.

`header('Feature-Flags', ForecastFeatureFlag::FORECAST_TEMPERATURE_QV)` asks for temperature as a `QuantitativeValue`. `FORECAST_WIND_SPEED_QV` does the same for wind speed. Without the flag, `temperature` is the number, `temperature_unit` is `F` or `C`, and `wind_speed` is the text (`5 mph`). With the flag, `temperature_reading` or `wind_speed_reading` holds the quantitative value, `temperature_unit` becomes that value's unit code (`wmoUnit:degC`), `wind_speed` is null, and `windSpeedText()` joins the number and the unit code.[^period]

`knownTrend()` is `TemperatureTrend::RISING` or `FALLING`, or null.

[^service]: ForecastAPIService
[^period]: ForecastPeriod
[^openapi]: weather.gov API OpenAPI 3.11.0
