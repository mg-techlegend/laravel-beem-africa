<?php

namespace TechLegend\LaravelBeemAfrica;

use Illuminate\Support\Facades\Notification;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use TechLegend\LaravelBeemAfrica\SMS\Beem as BeemSms;
use TechLegend\LaravelBeemAfrica\SMS\BeemChannel;
use TechLegend\LaravelBeemAfrica\OTP\BeemOtp;
use TechLegend\LaravelBeemAfrica\Airtime\BeemAirtime;
use TechLegend\LaravelBeemAfrica\USSD\BeemUssd;
use TechLegend\LaravelBeemAfrica\Voice\BeemVoice;
use TechLegend\LaravelBeemAfrica\Insights\BeemInsights;
use TechLegend\LaravelBeemAfrica\Webhooks\BeemWebhookHandler;

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

        // Register the main Beem facade
        $this->app->singleton(Beem::class, function ($app) {
            return new Beem($app['config']['beem']);
        });

        // Register individual services
        $this->app->bind(BeemSms::class, function ($app) {
            return new BeemSms($app['config']['beem']);
        });

        $this->app->bind(BeemOtp::class, function ($app) {
            return new BeemOtp($app['config']['beem']);
        });

        $this->app->bind(BeemAirtime::class, function ($app) {
            return new BeemAirtime($app['config']['beem']);
        });

        $this->app->bind(BeemUssd::class, function ($app) {
            return new BeemUssd($app['config']['beem']);
        });

        $this->app->bind(BeemVoice::class, function ($app) {
            return new BeemVoice($app['config']['beem']);
        });

        $this->app->bind(BeemInsights::class, function ($app) {
            return new BeemInsights($app['config']['beem']);
        });

        $this->app->bind(BeemWebhookHandler::class, function ($app) {
            $webhookSecret = $app['config']['beem']['webhooks']['secret'] ?? '';
            return new BeemWebhookHandler($webhookSecret);
        });

        // Register the notification channel
        Notification::extend('beem', function ($app) {
            return new BeemChannel($app->make(BeemSms::class));
        });
    }
}
