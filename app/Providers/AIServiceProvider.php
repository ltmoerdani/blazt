<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\AI\ChatbotInterface;
use App\Domain\AI\Services\ChatbotService;
use App\Domain\AI\Providers\ProviderManager;
use App\Domain\AI\Providers\OpenAIProvider;
use App\Domain\AI\Providers\DeepSeekProvider;
use App\Domain\AI\Providers\ClaudeProvider;

class AIServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(ProviderManager::class, function () {
            $manager = new ProviderManager();
            $manager->registerProvider('openai', new OpenAIProvider());
            $manager->registerProvider('deepseek', new DeepSeekProvider());
            $manager->registerProvider('claude', new ClaudeProvider());
            return $manager;
        });

        $this->app->singleton(ChatbotInterface::class, function ($app) {
            return new ChatbotService(
                $app->make(ProviderManager::class)
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
