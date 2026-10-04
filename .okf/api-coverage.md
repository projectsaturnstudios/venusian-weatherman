---
type: Reference
title: Weatherman API coverage
description: Core versus deferred status for every api.weather.gov family Weatherman catalogues. Every core family has an async lane.
tags:
  - coverage
  - nws
  - status
status: draft
generated:
  by: grok-4.7/2026-10-04
  at: '2026-10-04T03:45:00Z'
sources:
  - id: client
    resource: src/NwsClient.php
    title: NwsClient accessors
  - id: openapi
    resource: https://api.weather.gov/openapi.json
    title: weather.gov API OpenAPI 3.11.0
  - id: seam
    resource: /async-seam.md
    title: Async lane
---

# Overview

`NwsURL` holds one host, `https://api.weather.gov`. Five core families have builders, captured fixtures, Pest coverage, and an async lane. Eleven deferred families throw `NotYetSupportedException` from their constructor. The catalog matches OpenAPI 3.11.0.[^client][^openapi][^seam]

# Core

Every core family has an async lane: `async()` fulfils with what `get()` returns.

| Family | Accessor | Async | Concept |
|--------|----------|-------|---------|
| Points | `points()` | promise | [Points](/points.md) |
| Forecast | `forecast()` | promise | [Forecast](/forecast.md) |
| Alerts | `alerts()` | promise | [Alerts](/alerts.md) |
| Stations | `stations()` | promise | [Stations](/stations.md) |
| Observations | `observations()` | promise of `latest()` | [Observations](/observations.md) |

`observations()->list()` and `observations()->atTime()` are deferred methods on the live observations service. See [deferred APIs](/deferred-apis.md).

# Deferred

| Family | Accessor | OpenAPI paths |
|--------|----------|---------------|
| Aviation | `aviation()` | `/aviation/cwsus`, `/aviation/sigmets` |
| Glossary | `glossary()` | `/glossary` |
| Gridpoint data | `gridpoints()` | `/gridpoints/{wfo}/{x},{y}` raw quantitative grid |
| Icons | `icons()` | `/icons` |
| Satellite thumbnails | `thumbnails()` | `/thumbnails/satellite/{area}` |
| Offices | `offices()` | `/offices/{officeId}` and headlines, briefings, weather stories |
| Radar | `radar()` | `/radar/servers`, `/radar/stations`, queues, profilers |
| NOAA Weather Radio | `radio()` | `/radio`, `/points/.../radio` |
| Products | `products()` | `/products` |
| Zones | `zones()` | `/zones` |
| TAFs | `tafs()` | `/stations/{stationId}/tafs` |

The narrative forecast (`/gridpoints/{wfo}/{x},{y}/forecast` and `/forecast/hourly`) and the gridpoint station list are core. They are not the raw gridpoint document.

[^client]: NwsClient accessors
[^openapi]: weather.gov API OpenAPI 3.11.0
[^seam]: Async lane
