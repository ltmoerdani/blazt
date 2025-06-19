<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\User\Interfaces\RoleServiceInterface;
use App\Domain\User\Services\RoleService;

/**
 * User Service Provider
 *
 * Following DDD principles - Domain service registration
 * Handles dependency injection for User domain services
 */
class UserServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind Role Service Interface to Implementation
        $this->app->bind(RoleServiceInterface::class, RoleService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
