<?php

namespace Hanoivip\Proceed;

use Hanoivip\Proceed\Console\ProceedPersist;
use Illuminate\Support\ServiceProvider;

class ModServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/Views' => resource_path('views/vendor/hanoivip'),
            __DIR__.'/Langs' => resource_path('lang/vendor/hanoivip'),
        ]);
        $this->loadMigrationsFrom(__DIR__ . '/Migrations');
        $this->loadViewsFrom(__DIR__ . '/Views', 'hanoivip');
        $this->loadRoutesFrom(__DIR__ . '/Routes/web.php');
        $this->loadTranslationsFrom( __DIR__.'/Langs', 'hanoivip');
        $this->mergeConfigFrom( __DIR__.'/../config/proceed.php', 'proceed');
    }

    public function register()
    {
        $this->commands([
            ProceedPersist::class,
        ]);
    }
}
