# Update Log

## 2026-10-04
* **Creation**: `projectsaturnstudios/venusian-weatherman` 0.10.0 — National Weather Service client on the Stargazer seam (`nws()`, `NwsClient`, `PendingNwsRequest`, `get()` / `async()`). Core families are [points](points.md), [forecast](forecast.md), [alerts](alerts.md), [stations](stations.md), and [observations](observations.md). The rest of api.weather.gov OpenAPI 3.11.0 is catalogued as [deferred stubs](deferred-apis.md). An empty alert or station page does not expose `next()`, even when `pagination.next` is still set.
