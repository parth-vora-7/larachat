<?php

namespace xparthxvorax\larachat;

use Illuminate\Support\ServiceProvider;
use xparthxvorax\larachat\Commands\LaraChatInstall;

class LaraChatServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(realpath(__DIR__ . '/../routes/web.php'));
        $this->loadMigrationsFrom(realpath(__DIR__ . '/Migrations'));
        $this->publishes([
            realpath(__DIR__ . '/../publishable/assets/js/components') => resource_path('js/components'),
        ], 'larachat');
        $this->publishes([
            realpath(__DIR__ . '/../publishable/assets/img') => public_path('vendor/larachat'),
        ], 'larachat');
        $this->publishes([
            realpath(__DIR__ . '/../publishable/assets/laravel-echo-server.json') => base_path() . 'laravel-echo-server.json',
        ], 'larachat');
        if ($this->app->runningInConsole()) {
            $this->commands([
                LaraChatInstall::class,
            ]);
        }
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

    }
}
