<?php

namespace TechLegend\LaravelBeemAfrica;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use TechLegend\LaravelBeemAfrica\Commands\LaravelBeemAfricaCommand;

class LaravelBeemAfricaServiceProvider extends PackageServiceProvider
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
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel_beem_africa_table')
            ->hasCommand(LaravelBeemAfricaCommand::class);
    }
}
