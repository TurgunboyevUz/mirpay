<?php
namespace TurgunboyevUz\Mirpay;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use TurgunboyevUz\Mirpay\Commands\Install;
use TurgunboyevUz\Mirpay\Services\MirpayService;

class MirpayServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../publishable/config/mirpay.php', 'mirpay');

        $this->app->bind(MirpayService::class, function () {
            return new MirpayService();
        });
    }

    public function boot(): void
    {
        if (config('mirpay.routes') && file_exists(base_path('routes/mirpay.php'))) {
            Route::middleware('api')->prefix('api')->group(base_path('routes/mirpay.php'));
        }

        $this->publishes([
            __DIR__ . '/../publishable/config/mirpay.php'         => config_path('mirpay.php'),
            __DIR__ . '/../publishable/routes/mirpay.php'         => base_path('routes/mirpay.php'),
            __DIR__ . '/../publishable/Payment/after_success.php' => app_path('Payment/after_success.php'),
            __DIR__ . '/../publishable/Payment/after_fail.php'    => app_path('Payment/after_fail.php'),
        ], 'mirpay-assets');

        $this->publishesMigrations([
            __DIR__ . '/database/migrations' => database_path('migrations'),
        ], 'mirpay-database');

        if ($this->app->runningInConsole()) {
            $this->commands([
                Install::class,
            ]);
        }
    }
}
