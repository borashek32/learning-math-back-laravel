<?php

namespace App\Providers\Unisender;

use App\Helpers\UniSender\UniSenderApiService;
use Illuminate\Support\ServiceProvider;

class UnisenderServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(UniSenderApiService::class, function () {
            return UniSenderApiService::getInstance();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
