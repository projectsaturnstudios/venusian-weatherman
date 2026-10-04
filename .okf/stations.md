---
type: API Family
title: Stations
description: Observation station index, a single station, and the stations for a gridpoint.
tags:
  - stations
  - nws
status: draft
generated:
  by: grok-4.7/2026-10-04
  at: '2026-10-04T03:45:00Z'
sources:
  - id: service
    resource: src/Stations/StationsAPIService.php
    title: StationsAPIService
  - id: station
    resource: src/Stations/DataObjects/Station.php
    title: Station
  - id: openapi
    resource: https://api.weather.gov/openapi.json
    title: weather.gov API OpenAPI 3.11.0
---

# Overview

| Method | Path | Returns |
|--------|------|---------|
| `index(?string $state, ?int $limit)` | `GET /stations` | `StationPage` |
| `find(string $station_id)` | `GET /stations/{stationId}` | `Station` |
| `grid($office, $x, $y)` | `GET /gridpoints/{wfo}/{x},{y}/stations` | `StationPage` |

`$limit` must be 1 to 500. A station id is 3 to 10 letters or digits and is sent in uppercase. `with('cursor')` and `with('id')` add the other index parameters.[^service][^openapi]

`StationPage` holds `stations`, `observation_station_urls`, and `next()`. `next()` is null when the page has no features and no observation-station URLs, even if `pagination.next` is present. weather.gov keeps that link on the empty pages after a gridpoint station list, and each empty page points at another.[^station]

A point's `stations()` follows `observationStations`, which is this gridpoint path. It does not call `/points/{lat},{lon}/stations`, because that path responds 301.

[^service]: StationsAPIService
[^station]: Station
[^openapi]: weather.gov API OpenAPI 3.11.0
