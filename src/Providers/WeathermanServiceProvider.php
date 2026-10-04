<?php

namespace ProjectSaturnStudios\Weatherman\Providers;

use ProjectSaturnStudios\Weatherman\NwsClient;
use Voyager\NutsAndBolts\ServiceProvider;

class WeathermanServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            dirname(__DIR__, 2).'/config/nws.php',
            'nws',
        );

        $this->app->registerSingleton('nws', fn ($app) => new NwsClient(
            user_agent: $app['config']->get('nws.user_agent'),
            http: $app['http'],
        ));
    }

    public function boot(): void
    {
        $this->publishes([
            dirname(__DIR__, 2).'/config/nws.php' => $this->app->configPath('nws.php'),
        ], 'nws-config');
    }
}
