<?php

namespace TechLegend\LaravelBeemAfrica\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use TechLegend\LaravelBeemAfrica\LaravelBeemAfricaServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            LaravelBeemAfricaServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');
    }
}
