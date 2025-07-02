<?php

namespace TurgunboyevUz\Mirpay\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class Install extends Command
{
    protected $signature = 'mirpay:install';
    protected $description = 'Install Mirpay package';

    public function handle()
    {
        Artisan::call('vendor:publish', [
            '--tag' => 'mirpay-assets',
        ]);

        $this->info(Artisan::output());

        Artisan::call('vendor:publish', [
            '--tag' => 'mirpay-database',
        ]);

        $this->info(Artisan::output());

        $this->info('Mirpay package installed');
    }
}
