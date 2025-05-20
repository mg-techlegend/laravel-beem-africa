<?php

namespace TechLegend\LaravelBeemAfrica\Commands;

use Illuminate\Console\Command;

class LaravelBeemAfricaCommand extends Command
{
    public $signature = 'laravel-beem-africa';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
