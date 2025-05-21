<?php

namespace TechLegend\LaravelBeemAfrica\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use TechLegend\LaravelBeemAfrica\BeemServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            BeemServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('beem', [
            'api_key' => 'test_api_key',
            'secret_key' => 'test_secret_key',
            'sender_name' => 'TestSender',
        ]);
    }
}
