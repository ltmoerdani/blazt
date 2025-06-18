<?php

namespace App\Domain\AI\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class ClaudeProvider implements AIProviderInterface
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.claude.api_key');
        $this->baseUrl = config('services.claude.base_url', 'https://api.anthropic.com/v1');
    }

    public function generateResponse(string $prompt, array $context = [], array $options = []): string
    {
        try {
            $messages = [];

            if (!empty($context)) {
                $messages[] = ['role' => 'system', 'content' => 'You are a helpful assistant. Previous conversation context: ' . $context];
            }

            $messages[] = ['role' => 'user', 'content' => $prompt];

            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/messages', [
                'model' => 'claude-3-opus-20240229',
                'messages' => $messages,
                'max_tokens' => 1024,
                'temperature' => 0.7,
            ])->json();

            if (isset($response['content'][0]['text'])) {
                return $response['content'][0]['text'];
            } else {
                Log::error('Claude API response error: ' . json_encode($response));
                return 'Sorry, I could not generate a response.';
            }
        } catch (Exception $e) {
            Log::error('Error communicating with Claude API: ' . $e->getMessage());
            return 'Sorry, an error occurred while connecting to the AI.';
        }
    }

    public function getProviderName(): string
    {
        return 'claude';
    }

    public function validateConfiguration(array $config): bool
    {
        return isset($config['api_key']) && !empty($config['api_key']);
    }

    public function getAvailableModels(): array
    {
        return [
            'claude-3-haiku' => 'Claude 3 Haiku',
            'claude-3-sonnet' => 'Claude 3 Sonnet',
            'claude-3-opus' => 'Claude 3 Opus',
        ];
    }
}
