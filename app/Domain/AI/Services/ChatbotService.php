<?php

namespace App\Domain\AI\Services;

use App\Domain\User\Models\User;
use App\Domain\AI\Models\Conversation;
use App\Domain\AI\Models\ConversationMessage;
use App\Domain\AI\Models\AIConfiguration;
use App\Domain\Contact\Models\Contact;
use App\Domain\WhatsApp\Models\WhatsAppAccount;
use App\Domain\AI\Providers\AIProviderInterface;
use App\Domain\AI\Providers\ProviderManager;
use Illuminate\Support\Facades\Log;
use Exception;

class ChatbotService
{
    protected $providerManager;

    public function __construct(ProviderManager $providerManager)
    {
        $this->providerManager = $providerManager;
    }

    public function startConversation(User $user, WhatsAppAccount $whatsappAccount, Contact $contact, string $initialMessage): Conversation
    {
        // Create a new conversation
        $conversation = Conversation::create([
            'user_id' => $user->id,
            'whatsapp_account_id' => $whatsappAccount->id,
            'contact_id' => $contact->id,
            'status' => 'active',
            'last_message_at' => now(),
            'ai_model_used' => $this->getDefaultAIProviderName($user), // Get default provider
            'context_data' => [],
        ]);

        // Add the initial message to the conversation
        $conversation->messages()->create([
            'sender_type' => 'contact',
            'message_content' => $initialMessage,
            'message_type' => 'text',
            'timestamp' => now(),
        ]);

        Log::info('New conversation started: ' . $conversation->id);

        return $conversation;
    }

    public function processChatMessage(Conversation $conversation, string $messageContent, string $senderType = 'contact'): ConversationMessage
    {
        // Add the new message to the conversation
        $conversationMessage = $conversation->messages()->create([
            'sender_type' => $senderType,
            'message_content' => $messageContent,
            'message_type' => 'text',
            'timestamp' => now(),
        ]);

        $conversation->update(['last_message_at' => now()]);

        // Get the AI provider based on conversation's AI model used or user's default
        $aiConfig = AIConfiguration::where('user_id', $conversation->user_id)->where('active', true)->first();
        $aiProvider = $this->providerManager->getProvider($aiConfig->provider ?? 'openai'); // Default to openai

        $context = $this->buildConversationContext($conversation);
        $aiResponse = $aiProvider->generateResponse($messageContent, [$context]);

        // Add AI's response to the conversation
        $conversation->messages()->create([
            'sender_type' => 'ai',
            'message_content' => $aiResponse,
            'message_type' => 'text',
            'timestamp' => now(),
        ]);

        Log::info('Processed message for conversation: ' . $conversation->id);

        return $conversationMessage;
    }

    protected function buildConversationContext(Conversation $conversation): string
    {
        // Build context from past messages in the conversation
        return $conversation->messages()
            ->latest('timestamp')
            ->limit(10) // Limit context to last 10 messages
            ->get()
            ->reverse()
            ->map(function ($message) {
                return $message->sender_type . ': ' . $message->message_content;
            })
            ->implode("\n");
    }

    protected function getDefaultAIProviderName(User $user): string
    {
        // Retrieve user's default AI provider from AIConfiguration
        $aiConfig = AIConfiguration::where('user_id', $user->id)->where('active', true)->first();
        return $aiConfig->provider ?? 'openai';
    }

    public function updateConversationStatus(Conversation $conversation, string $status): bool
    {
        return $conversation->update(['status' => $status]);
    }

    public function generateResponse(string $message, array $context = [], array $options = []): ?string
    {
        $result = null;
        
        try {
            $userId = $options['user_id'] ?? null;
            $user = $userId ? User::find($userId) : null;
            
            if ($user) {
                // Get AI configuration for user
                $aiConfig = $this->getAIConfiguration($user);
                if ($aiConfig) {
                    $result = $this->generateAIResponse($message, $context, $aiConfig, $user);
                } else {
                    Log::warning('No AI configuration found for user', ['user_id' => $user->id]);
                }
            } else {
                Log::warning('User not found for AI request', ['user_id' => $userId]);
            }

        } catch (Exception $e) {
            Log::error('Failed to generate AI response', [
                'message' => $message,
                'error' => $e->getMessage(),
            ]);
        }
        
        return $result ?? $this->getFallbackMessage();
    }

    protected function generateAIResponse(string $message, array $context, AIConfiguration $aiConfig, User $user): string
    {
        // Get AI provider
        $provider = $this->providerManager->getProvider($aiConfig->provider);
        
        // Generate response using AI provider
        $response = $provider->generateResponse($message, $context, [
            'model' => $aiConfig->model,
            'temperature' => $aiConfig->temperature,
            'max_tokens' => $aiConfig->max_tokens,
        ]);

        Log::info('AI response generated', [
            'user_id' => $user->id,
            'provider' => $aiConfig->provider,
            'message_length' => strlen($message),
            'response_length' => strlen($response),
        ]);

        return $response;
    }

    protected function getFallbackMessage(): string
    {
        return config('ai-providers.chatbot.fallback_message');
    }

    protected function getAIConfiguration(User $user): ?AIConfiguration
    {
        // Get active AI configuration for user
        return $user->aiConfigurations()
            ->where('active', true)
            ->first() ?: $this->createDefaultAIConfiguration($user);
    }

    protected function createDefaultAIConfiguration(User $user): AIConfiguration
    {
        $defaultProvider = config('ai-providers.default');
        $providerConfig = config("ai-providers.providers.{$defaultProvider}");

        return $user->aiConfigurations()->create([
            'provider' => $defaultProvider,
            'model' => $providerConfig['default_model'],
            'temperature' => $providerConfig['default_temperature'],
            'max_tokens' => $providerConfig['default_max_tokens'],
            'active' => true,
        ]);
    }
}
