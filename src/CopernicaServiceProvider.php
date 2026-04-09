<?php

namespace Budgetlens\Copernica\RestClient;

use Illuminate\Support\ServiceProvider;

class CopernicaServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/copernica.php', 'copernica');

        $this->app->singleton(CopernicaClient::class, function ($app) {
            $config = $app['config']['copernica'];

            if (empty($config['access_token'])) {
                throw new \InvalidArgumentException(
                    'Copernica access token is not configured. Set COPERNICA_ACCESS_TOKEN in your .env file.'
                );
            }

            return new CopernicaClient(
                accessToken: $config['access_token'],
                baseUrl: $config['base_url'],
                timeout: $config['timeout'],
            );
        });

        $this->app->alias(CopernicaClient::class, 'copernica');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/copernica.php' => config_path('copernica.php'),
        ], 'copernica-config');
    }
}
