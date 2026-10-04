<?php

use ProjectSaturnStudios\Weatherman\Aviation\AviationAPIService;
use ProjectSaturnStudios\Weatherman\Exceptions\NotYetSupportedException;
use ProjectSaturnStudios\Weatherman\Glossary\GlossaryAPIService;
use ProjectSaturnStudios\Weatherman\Gridpoints\GridpointAPIService;
use ProjectSaturnStudios\Weatherman\Icons\IconsAPIService;
use ProjectSaturnStudios\Weatherman\NwsClient;
use ProjectSaturnStudios\Weatherman\Offices\OfficesAPIService;
use ProjectSaturnStudios\Weatherman\Products\ProductsAPIService;
use ProjectSaturnStudios\Weatherman\Radar\RadarAPIService;
use ProjectSaturnStudios\Weatherman\Radio\RadioAPIService;
use ProjectSaturnStudios\Weatherman\Tafs\TafsAPIService;
use ProjectSaturnStudios\Weatherman\Thumbnails\ThumbnailsAPIService;
use ProjectSaturnStudios\Weatherman\Zones\ZonesAPIService;

it('throws NotYetSupportedException from every deferred API stub', function (string $class, string $name) {
    expect(fn () => new $class())
        ->toThrow(NotYetSupportedException::class, $name.' is not yet supported by Weatherman.');
})->with([
    'Aviation' => [AviationAPIService::class, 'Aviation'],
    'Glossary' => [GlossaryAPIService::class, 'Glossary'],
    'Gridpoint data' => [GridpointAPIService::class, 'Gridpoint data'],
    'Icons' => [IconsAPIService::class, 'Icons'],
    'Satellite thumbnails' => [ThumbnailsAPIService::class, 'Satellite thumbnails'],
    'Offices' => [OfficesAPIService::class, 'Offices'],
    'Radar' => [RadarAPIService::class, 'Radar'],
    'NOAA Weather Radio' => [RadioAPIService::class, 'NOAA Weather Radio'],
    'Products' => [ProductsAPIService::class, 'Products'],
    'Zones' => [ZonesAPIService::class, 'Zones'],
    'TAFs' => [TafsAPIService::class, 'TAFs'],
]);

it('exposes deferred accessors that throw immediately', function (string $method, string $name) {
    expect(fn () => (new NwsClient(user_agent: 'test'))->{$method}())
        ->toThrow(NotYetSupportedException::class, $name.' is not yet supported by Weatherman.');
})->with([
    'aviation' => ['aviation', 'Aviation'],
    'glossary' => ['glossary', 'Glossary'],
    'gridpoints' => ['gridpoints', 'Gridpoint data'],
    'icons' => ['icons', 'Icons'],
    'thumbnails' => ['thumbnails', 'Satellite thumbnails'],
    'offices' => ['offices', 'Offices'],
    'radar' => ['radar', 'Radar'],
    'radio' => ['radio', 'NOAA Weather Radio'],
    'products' => ['products', 'Products'],
    'zones' => ['zones', 'Zones'],
    'tafs' => ['tafs', 'TAFs'],
]);

it('names the observation history and time endpoints that are still stubs', function () {
    $observations = (new NwsClient(user_agent: 'test'))->observations();

    expect(fn () => $observations->list('KDCA'))
        ->toThrow(NotYetSupportedException::class, 'Station observation history for KDCA is not yet supported by Weatherman.')
        ->and(fn () => $observations->atTime('KDCA', '2026-10-04T03:05:00+00:00'))
        ->toThrow(NotYetSupportedException::class, 'Station observation at 2026-10-04T03:05:00+00:00 for KDCA is not yet supported by Weatherman.');
});
