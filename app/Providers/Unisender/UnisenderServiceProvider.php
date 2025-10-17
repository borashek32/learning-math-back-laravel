<?php

namespace App\Providers\Unisender;

use App\Helpers\Unisender\UnisenderApiService;
use Illuminate\Support\ServiceProvider;

class UnisenderServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(UnisenderApiService::class, function() {
            return UnisenderApiService::getInstance();
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
