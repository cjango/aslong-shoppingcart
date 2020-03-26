<?php

namespace AsLong\Cart;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__ . '/../../config/config.php' => config_path('cart.php')]);
            $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations/');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/config.php', 'cart');
    }

}
