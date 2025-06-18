<?php

namespace App\Domain\AI\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Domain\WhatsApp\Models\WhatsAppMessage;
use App\Domain\AI\Services\ChatbotService;
use App\Domain\WhatsApp\Services\MessageSender;
use App\Domain\Analytics\Services\AnalyticsService;
use Illuminate\Support\Facades\Log;
use Exception;

class ProcessAIResponseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected WhatsAppMessage $incomingMessage;

    /**
     * Create a new job instance.
     */
    public function __construct(WhatsAppMessage $incomingMessage)
    {
        $this->incomingMessage = $incomingMessage;
    }

    /**
     * Execute the job.
     */
    public function handle(
        ChatbotService $chatbotService,
        MessageSender $messageSender,
        AnalyticsService $analyticsService
    ): void {
        try {
            Log::info('Processing AI response for message', [
                'message_id' => $this->incomingMessage->id,
                'from' => $this->incomingMessage->phone_number,
            ]);

            // Check if user has AI enabled and within limits
            if (!$this->canProcessAIRequest()) {
                Log::info('AI request skipped - limits exceeded or disabled');
                return;
            }

            // Get conversation context
            $context = $this->getConversationContext();

            // Generate AI response
            $response = $chatbotService->generateResponse(
                $this->incomingMessage->message_content,
                $context,
                [
                    'user_id' => $this->incomingMessage->user_id,
                    'phone_number' => $this->incomingMessage->phone_number,
                ]
            );

            if ($response) {
                // Send AI response back to user
                $result = $messageSender->sendMessage(
                    $this->incomingMessage->whatsappAccount,
                    $this->incomingMessage->phone_number,
                    $response,
                    null, // No campaign ID for AI responses
                    'ai_response'
                );

                if ($result['success']) {
                    // Track AI usage
                    $analyticsService->recordUserActivity(
                        $this->incomingMessage->user,
                        'ai_request',
                        1,
                        [
                            'incoming_message_id' => $this->incomingMessage->id,
                            'response_length' => strlen($response),
                        ]
                    );

                    Log::info('AI response sent successfully', [
                        'message_id' => $this->incomingMessage->id,
                        'response_length' => strlen($response),
                    ]);
                } else {
                    Log::error('Failed to send AI response', [
                        'message_id' => $this->incomingMessage->id,
                        'error' => $result['error'] ?? 'Unknown error',
                    ]);
                }
            }

        } catch (Exception $e) {
            Log::error('AI response processing failed', [
                'message_id' => $this->incomingMessage->id,
                'error' => $e->getMessage(),
            ]);
            
            // Send fallback message
            $this->sendFallbackMessage();
        }
    }

    /**
     * Check if AI request can be processed
     */
    private function canProcessAIRequest(): bool
    {
        $user = $this->incomingMessage->user;
        
        if (!$user) {
            return false;
        }

        $userLimit = $user->userLimit;
        if (!$userLimit) {
            return false;
        }

        // Check daily AI request limit
        $todayRequests = $user->usageLogs()
            ->where('activity_type', 'ai_request')
            ->whereDate('created_at', today())
            ->count();

        return $todayRequests < $userLimit->ai_requests_daily_limit;
    }

    /**
     * Get conversation context for AI
     */
    private function getConversationContext(): array
    {
        $recentMessages = WhatsAppMessage::where('phone_number', $this->incomingMessage->phone_number)
            ->where('whatsapp_account_id', $this->incomingMessage->whatsapp_account_id)
            ->orderBy('created_at', 'desc')
            ->limit(5) // Last 5 messages for context
            ->get()
            ->reverse()
            ->values();

        $context = [];
        foreach ($recentMessages as $message) {
            $context[] = [
                'role' => $message->direction === 'incoming' ? 'user' : 'assistant',
                'content' => $message->message_content,
                'timestamp' => $message->created_at->toISOString(),
            ];
        }

        return $context;
    }

    /**
     * Send fallback message when AI fails
     */
    private function sendFallbackMessage(): void
    {
        try {
            $fallbackMessage = config('ai-providers.auto_reply.default_responses.fallback');
            
            $messageSender = app(MessageSender::class);
            $messageSender->sendMessage(
                $this->incomingMessage->whatsappAccount,
                $this->incomingMessage->phone_number,
                $fallbackMessage,
                null,
                'fallback'
            );
            
        } catch (Exception $e) {
            Log::error('Failed to send fallback message', [
                'message_id' => $this->incomingMessage->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * The job failed to process.
     */
    public function failed(Exception $exception): void
    {
        Log::error('ProcessAIResponseJob failed', [
            'message_id' => $this->incomingMessage->id,
            'error' => $exception->getMessage(),
        ]);

        // Send fallback message
        $this->sendFallbackMessage();
    }
}
