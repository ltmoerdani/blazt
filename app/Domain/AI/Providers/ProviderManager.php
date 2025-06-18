<?php

namespace App\Domain\AI\Providers;

use InvalidArgumentException;

class ProviderManager
{
    protected $providers = [];

    public function registerProvider(string $name, AIProviderInterface $provider)
    {
        $this->providers[$name] = $provider;
    }

    public function getProvider(string $name): AIProviderInterface
    {
        if (!isset($this->providers[$name])) {
            throw new InvalidArgumentException("AI provider [{$name}] is not registered.");
        }

        return $this->providers[$name];
    }
}
