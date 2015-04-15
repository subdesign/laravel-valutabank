<?php

namespace Subdesign\Valutabank\Provider;

use Illuminate\Support\ServiceProvider;
use Subdesign\Valutabank\Valutabank;

/**
 * Valutabank.hu parser Service Provider
 */
class ValutabankServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/valutabank.php' => config_path('valutabank.php'),
        ]);

        $this->loadTranslationsFrom(__DIR__.'/../lang', 'valutabank');
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->bind('valutabank', function ($app) {
            return new Valutabank();
        });
    }
}
