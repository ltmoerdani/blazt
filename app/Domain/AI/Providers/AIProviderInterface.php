<?php

namespace App\Domain\AI\Providers;

interface AIProviderInterface
{
    public function generateResponse(string $prompt, array $context = [], array $options = []): string;
    public function getProviderName(): string;
    public function validateConfiguration(array $config): bool;
    public function getAvailableModels(): array;
}
