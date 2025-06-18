<?php

namespace App\Domain\WhatsApp\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Domain\WhatsApp\Models\WhatsAppMessage;
use App\Domain\Analytics\Services\AnalyticsService;
use Illuminate\Support\Facades\Log;
use Exception;

class ProcessIncomingMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $messageData;

    /**
     * Create a new job instance.
     */
    public function __construct(array $messageData)
    {
        $this->messageData = $messageData;
    }

    /**
     * Execute the job.
     */
    public function handle(AnalyticsService $analyticsService): void
    {
        try {
            Log::info('Processing incoming WhatsApp message', [
                'from' => $this->messageData['from'] ?? 'unknown',
                'message_id' => $this->messageData['id'] ?? 'unknown',
            ]);

            // Find or create WhatsApp message record
            $message = WhatsAppMessage::updateOrCreate([
                'message_id' => $this->messageData['id'],
            ], [
                'whatsapp_account_id' => $this->messageData['whatsapp_account_id'],
                'user_id' => $this->messageData['user_id'],
                'contact_id' => $this->messageData['contact_id'] ?? null,
                'phone_number' => $this->messageData['from'],
                'message_content' => $this->messageData['body'] ?? '',
                'message_type' => $this->messageData['type'] ?? 'text',
                'direction' => 'incoming',
                'status' => 'received',
                'media_path' => $this->messageData['media_path'] ?? null,
                'timestamp' => $this->messageData['timestamp'] ?? now(),
            ]);

            // Process message for AI auto-reply if enabled
            if ($this->shouldTriggerAutoReply($message)) {
                $this->triggerAutoReply($message);
            }

            // Update contact last message timestamp
            if ($message->contact_id) {
                $message->contact->update(['last_message_at' => now()]);
            }

            // Track analytics
            $analyticsService->recordUserActivity(
                $message->user,
                'message_received',
                1,
                [
                    'message_id' => $message->id,
                    'phone_number' => $message->phone_number,
                    'message_type' => $message->message_type,
                ]
            );

            Log::info('Incoming message processed successfully', [
                'message_id' => $message->id,
                'from' => $message->phone_number,
            ]);

        } catch (Exception $e) {
            Log::error('Failed to process incoming message', [
                'message_data' => $this->messageData,
                'error' => $e->getMessage(),
            ]);
            
            throw $e;
        }
    }

    /**
     * Check if auto-reply should be triggered
     */
    private function shouldTriggerAutoReply(WhatsAppMessage $message): bool
    {
        // Don't auto-reply to outgoing messages
        if ($message->direction === 'outgoing') {
            return false;
        }
        // Check if user exists
        $user = $message->user;
        if (!$user) {
            return false;
        }
        // Check business hours and keywords
        $outsideBusinessHours = $this->isOutsideBusinessHours();
        $hasKeywords = $this->hasAutoReplyKeywords($message->message_content);
        return $outsideBusinessHours || $hasKeywords;
    }

    /**
     * Check if current time is outside business hours
     */
    private function isOutsideBusinessHours(): bool
    {
        $config = config('ai-providers.auto_reply.business_hours');
        
        if (!$config['enabled']) {
            return false;
        }

        $now = now($config['timezone']);
        $dayOfWeek = strtolower($now->format('l'));
        
        $hours = $config['hours'][$dayOfWeek] ?? null;
        
        if (!$hours) {
            return true; // Closed day
        }

        $currentTime = $now->format('H:i');
        return $currentTime < $hours[0] || $currentTime > $hours[1];
    }

    /**
     * Check if message contains auto-reply keywords
     */
    private function hasAutoReplyKeywords(string $content): bool
    {
        $keywords = ['halo', 'hai', 'hello', 'info', 'help', 'bantuan'];
        $content = strtolower($content);
        
        foreach ($keywords as $keyword) {
            if (strpos($content, $keyword) !== false) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Trigger auto-reply response
     */
    private function triggerAutoReply(WhatsAppMessage $message): void
    {
        // Queue auto-reply job
        \App\Domain\AI\Jobs\ProcessAIResponseJob::dispatch($message);
    }

    /**
     * The job failed to process.
     */
    public function failed(Exception $exception): void
    {
        Log::error('ProcessIncomingMessageJob failed', [
            'message_data' => $this->messageData,
            'error' => $exception->getMessage(),
        ]);
    }
}
