<?php

namespace TechLegend\LaravelBeemAfrica;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use TechLegend\LaravelBeemAfrica\Channels\BeemAfricaChannel;
use TechLegend\LaravelBeemAfrica\Webhooks\BeemWebhookHandler;

class LaravelBeemAfricaServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-beem-africa')
            ->hasConfigFile('beem-africa');
    }

    public function packageRegistered(): void
    {
        // 1. Register HTTP client
        $this->app->singleton(BeemAfricaClient::class, function ($app) {
            return BeemAfricaClient::fromConfig($app['config']->get('beem-africa'));
        });

        // 2. Register webhook handler
        $this->app->singleton(BeemWebhookHandler::class, function ($app) {
            $secret = $app['config']->get('beem-africa.webhooks.secret', '');

            return new BeemWebhookHandler($secret ?? '');
        });

        // 3. Register main entry point (depends on client + webhook handler)
        $this->app->singleton(LaravelBeemAfrica::class, function ($app) {
            return new LaravelBeemAfrica(
                $app->make(BeemAfricaClient::class),
                $app->make(BeemWebhookHandler::class),
            );
        });

        // 4. Register notification channel (depends on entry point)
        $this->app->singleton(BeemAfricaChannel::class, function ($app) {
            return new BeemAfricaChannel($app->make(LaravelBeemAfrica::class));
        });

        // 5. Container alias
        $this->app->alias(LaravelBeemAfrica::class, 'beem-africa');
    }
}
