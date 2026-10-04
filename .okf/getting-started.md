---
type: Reference
title: Getting started — Venusian Weatherman
description: How a Venusian sketch reaches api.weather.gov through nws(), NwsClient, a per-API service, and PendingNwsRequest.
tags:
  - getting-started
  - weatherman
  - venusian
status: draft
generated:
  by: grok-4.7/2026-10-04
  at: '2026-10-04T03:45:00Z'
sources:
  - id: nws-helper
    resource: src/helpers.php
    title: nws() helper
  - id: nws-client
    resource: src/NwsClient.php
    title: NwsClient accessors
  - id: provider
    resource: src/Providers/WeathermanServiceProvider.php
    title: WeathermanServiceProvider
  - id: config
    resource: config/nws.php
    title: nws.user_agent config
---

# Overview

`nws()` resolves `app('nws')`, the `NwsClient` singleton. The service provider registers that client and merges `config/nws.php` (`user_agent` from `NWS_USER_AGENT`).[^nws-helper][^provider]

api.weather.gov rejects a request with no User-Agent. The config default identifies this library. An application sets `NWS_USER_AGENT` to its own name and a contact URL or email. A client constructed with an empty agent, and no config value, throws `WeathermanException` before the request is sent.[^config]

A sketch calls a per-API accessor (`points()`, `forecast()`, `alerts()`, …) and then a builder method. The builder returns a [`PendingNwsRequest`](/architecture.md). `get()` is synchronous; `async()` follows the [async lane](/async-seam.md).[^nws-client]

Deferred families (`aviation()`, `radar()`, …) throw [`NotYetSupportedException`](/deferred-apis.md) until those leaves exist.

# Related

* [Architecture](/architecture.md) — the builder/DTO/enum pattern.
* [API coverage](/api-coverage.md) — which families ship and which are stubs.

[^nws-helper]: nws() helper
[^nws-client]: NwsClient accessors
[^provider]: WeathermanServiceProvider
[^config]: nws.user_agent config
