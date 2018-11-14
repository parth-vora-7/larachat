<?php

namespace xparthxvorax\larachat;

use Illuminate\Support\ServiceProvider;

class LaraChatServiceProvider extends ServiceProvider
{
    //https://bootsnipp.com/snippets/ORAPE
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(realpath(__DIR__.'/../routes/web.php'));
        $this->loadMigrationsFrom(realpath(__DIR__.'/../Migrations'));
        $this->publishes([
            realpath(__DIR__.'/../publishable/assets/js/components') => resource_path('js/components'),
        ], 'larachat');
        $this->publishes([
            realpath(__DIR__.'/../publishable/assets/img') => public_path('vendor/larachat'),
        ], 'larachat');
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
