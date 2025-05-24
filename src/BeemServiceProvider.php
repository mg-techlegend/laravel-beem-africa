<?php

namespace TechLegend\LaravelBeemAfrica;

use Spatie\LaravelPackageTools\Package;
use TechLegend\LaravelBeemAfrica\SMS\Beem;
use Illuminate\Support\Facades\Notification;
use TechLegend\LaravelBeemAfrica\SMS\BeemChannel;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class BeemServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-beem-africa')
            ->hasConfigFile('beem');

        Notification::extend('beem', function ($app) {
            return new BeemChannel($app->make(Beem::class));
        });

        $this->app->bind(Beem::class, function ($app) {
            return new Beem($app['config']['beem']);
        });
    }
}
