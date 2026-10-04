---
type: API Family
title: Points
description: Latitude,longitude metadata from /points, including the forecast, hourly, and station links NWS publishes.
tags:
  - points
  - nws
status: draft
generated:
  by: grok-4.7/2026-10-04
  at: '2026-10-04T03:45:00Z'
sources:
  - id: service
    resource: src/Points/PointsAPIService.php
    title: PointsAPIService
  - id: point
    resource: src/Points/DataObjects/Point.php
    title: Point
  - id: openapi
    resource: https://api.weather.gov/openapi.json
    title: weather.gov API OpenAPI 3.11.0
---

# Overview

`points()->at($latitude, $longitude)` requests `GET /points/{latitude},{longitude}`. Coordinates are rounded to four decimal places. A latitude outside -90..90 or a longitude outside -180..180 throws `InvalidArgumentException` before the request.[^service][^openapi]

The document is a GeoJSON Feature. `Point` reads `properties` and the feature `geometry`. `knownKind()` is `PointKind::LAND` or `MARINE`.[^point]

# Links

| Method | Property followed | Result |
|--------|-------------------|--------|
| `forecast()` | `forecast` | [Forecast](/forecast.md) |
| `hourly()` | `forecastHourly` | [Forecast](/forecast.md) |
| `stations()` | `observationStations` | [Station page](/stations.md) |

`/points/{latitude},{longitude}/stations` responds 301 with a problem document. `stations()` does not call that path. It calls the `observationStations` URL, which is `/gridpoints/{office}/{x},{y}/stations`.[^point]

`relative_location`, `astronomical_data`, and `radio` are hydrated when present. Radio playback itself is the deferred [radio](/deferred-apis.md) family.

[^service]: PointsAPIService
[^point]: Point
[^openapi]: weather.gov API OpenAPI 3.11.0
