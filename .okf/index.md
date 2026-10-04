---
okf_version: "0.2"
---

# Venusian Weatherman

National Weather Service client for Venusian (`projectsaturnstudios/venusian-weatherman` 0.10.0). Start here, then open only the concepts the task needs.

* [Getting started](getting-started.md) - how a sketch reaches api.weather.gov through the `nws()` helper, client, service, and pending request.

# Architecture

* [Architecture](architecture.md) - builder, DTO, and `NwsURL` pattern every core family shares.
* [Async lane](async-seam.md) - `get()` blocks; `async()` returns a loop promise of the same DTOs.
* [API coverage](api-coverage.md) - core versus deferred status; every core family has an async lane.

# Core API families

* [Points](points.md) - latitude,longitude metadata and the forecast, hourly, and station links it publishes.
* [Forecast](forecast.md) - 12-hour and hourly narrative forecasts for a gridpoint.
* [Alerts](alerts.md) - active alerts, the full collection, count, and event types.
* [Stations](stations.md) - observation station index, lookup, and gridpoint station lists.
* [Observations](observations.md) - the latest observation at a station.

# Deferred

* [Deferred APIs](deferred-apis.md) - aviation, glossary, raw gridpoint data, icons, thumbnails, offices, radar, radio, products, zones, and TAFs.
