<?php

namespace TechLegend\LaravelBeemAfrica\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \TechLegend\LaravelBeemAfrica\LaravelBeemAfrica
 */
class LaravelBeemAfrica extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \TechLegend\LaravelBeemAfrica\LaravelBeemAfrica::class;
    }
}
