<?php

namespace Hanoivip\Proceed;

use Illuminate\Support\ServiceProvider;

class ModServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/Views' => resource_path('views/vendor/hanoivip'),
            __DIR__.'/Langs' => resource_path('lang/vendor/hanoivip'),
        ]);
        $this->loadViewsFrom(__DIR__ . '/Views', 'hanoivip');
        $this->loadRoutesFrom(__DIR__ . '/Routes/web.php');
        $this->loadTranslationsFrom( __DIR__.'/Langs', 'hanoivip');
    }

    public function register()
    {
    }
}
