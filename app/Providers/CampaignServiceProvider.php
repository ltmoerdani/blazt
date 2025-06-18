<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\Campaign\CampaignServiceInterface;
use App\Domain\Campaign\Services\CampaignService;

class CampaignServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(CampaignServiceInterface::class, function ($app) {
            return new CampaignService(
                $app->make(\App\Domain\WhatsApp\Services\MessageSender::class)
            );
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
