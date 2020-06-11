<?php

namespace Williamjulianvicary\LaravelJobResponse;

use Illuminate\Support\ServiceProvider;
use Williamjulianvicary\LaravelJobResponse\Transport\TransportContract;

class LaravelJobResponseServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('job-response.php'),
            ], 'config');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'job-response');

        $this->app->singleton('laravel-job-response', function () {
            return new LaravelJobResponse;
        });

        $this->app->singleton(TransportFactory::class, function($app) {
           return new TransportFactory;
        });

        $this->app->bind(TransportContract::class, function($app) {
            return $app->make(TransportFactory::class)->getTransport(config('job-response.transport'));
        });
    }
}
