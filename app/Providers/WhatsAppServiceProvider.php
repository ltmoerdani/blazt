<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\WhatsApp\WhatsAppServiceInterface;
use App\Domain\WhatsApp\Services\WhatsAppService;
use App\Interfaces\WhatsApp\SessionManagerInterface;
use App\Domain\WhatsApp\Services\SessionManager;
use App\Interfaces\WhatsApp\MessageSenderInterface;
use App\Domain\WhatsApp\Services\MessageSender;
use App\Domain\WhatsApp\Repositories\WhatsAppAccountRepository;
use App\Domain\WhatsApp\Services\QRCodeGenerator;

class WhatsAppServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(WhatsAppAccountRepository::class, function () {
            return new WhatsAppAccountRepository();
        });

        $this->app->singleton(QRCodeGenerator::class, function () {
            return new QRCodeGenerator();
        });

        $this->app->singleton(MessageSenderInterface::class, function () {
            return new MessageSender();
        });

        $this->app->singleton(SessionManagerInterface::class, function () {
            return new SessionManager();
        });

        $this->app->singleton(WhatsAppServiceInterface::class, function ($app) {
            return new WhatsAppService(
                $app->make(WhatsAppAccountRepository::class),
                $app->make(SessionManagerInterface::class),
                $app->make(MessageSenderInterface::class),
                $app->make(QRCodeGenerator::class)
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