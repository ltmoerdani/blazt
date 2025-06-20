<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\EnhancedWhatsAppService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register Enhanced WhatsApp Service
        $this->app->singleton(EnhancedWhatsAppService::class, function () {
            return new EnhancedWhatsAppService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
