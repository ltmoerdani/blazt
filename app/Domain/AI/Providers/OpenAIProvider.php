<?php

namespace App\Domain\AI\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class OpenAIProvider implements AIProviderInterface
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
        $this->baseUrl = config('services.openai.base_url', 'https://api.openai.com/v1');
    }

    public function generateResponse(string $prompt, array $context = [], array $options = []): string
    {
        try {
            $messages = [];

            // Add system context if provided
            if (!empty($context)) {
                $messages[] = [
                    'role' => 'system',
                    'content' => 'You are a helpful WhatsApp chatbot assistant. Be concise and friendly.'
                ];
                
                // Add conversation context
                foreach ($context as $msg) {
                    $messages[] = $msg;
                }
            }

            // Add current prompt
            $messages[] = ['role' => 'user', 'content' => $prompt];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($this->baseUrl . '/chat/completions', [
                'model' => $options['model'] ?? 'gpt-3.5-turbo',
                'messages' => $messages,
                'temperature' => $options['temperature'] ?? 0.7,
                'max_tokens' => $options['max_tokens'] ?? 150,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['choices'][0]['message']['content'] ?? 'Sorry, I could not generate a response.';
            }

            Log::error('OpenAI API error: ' . $response->body());
            return 'Sorry, I am having trouble processing your request right now.';

        } catch (Exception $e) {
            Log::error('OpenAI Provider error: ' . $e->getMessage());
            return 'Sorry, I am temporarily unavailable.';
        }
    }

    public function getProviderName(): string
    {
        return 'openai';
    }

    public function validateConfiguration(array $config): bool
    {
        return isset($config['api_key']) && !empty($config['api_key']);
    }

    public function getAvailableModels(): array
    {
        return [
            'gpt-3.5-turbo' => 'GPT-3.5 Turbo',
            'gpt-4' => 'GPT-4',
            'gpt-4-turbo' => 'GPT-4 Turbo',
        ];
    }
}
