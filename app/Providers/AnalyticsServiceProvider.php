<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\Analytics\AnalyticsServiceInterface;
use App\Domain\Analytics\Services\AnalyticsService;

class AnalyticsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(AnalyticsServiceInterface::class, function ($app) {
            return new AnalyticsService();
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