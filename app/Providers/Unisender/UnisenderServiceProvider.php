<?php

declare(strict_types=1);

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
        $this->app->singleton(UniSenderApiService::class, fn () => UniSenderApiService::getInstance());
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void {}
}
