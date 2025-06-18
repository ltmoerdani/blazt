<?php

namespace App\Domain\AI\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class DeepSeekProvider implements AIProviderInterface
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.deepseek.api_key');
        $this->baseUrl = config('services.deepseek.base_url', 'https://api.deepseek.com/v1');
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
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/chat/completions', [
                'model' => 'deepseek-chat',
                'messages' => $messages,
                'max_tokens' => 150,
                'temperature' => 0.7,
            ])->json();

            if (isset($response['choices'][0]['message']['content'])) {
                return $response['choices'][0]['message']['content'];
            } else {
                Log::error('DeepSeek API response error: ' . json_encode($response));
                return 'Sorry, I could not generate a response.';
            }
        } catch (Exception $e) {
            Log::error('Error communicating with DeepSeek API: ' . $e->getMessage());
            return 'Sorry, an error occurred while connecting to the AI.';
        }
    }

    public function getProviderName(): string
    {
        return 'deepseek';
    }

    public function validateConfiguration(array $config): bool
    {
        return isset($config['api_key']) && !empty($config['api_key']);
    }

    public function getAvailableModels(): array
    {
        return [
            'deepseek-chat' => 'DeepSeek Chat',
            'deepseek-coder' => 'DeepSeek Coder',
        ];
    }
}
