<?php

namespace App\Domain\WhatsApp\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Domain\Campaign\Models\Campaign;
use App\Domain\WhatsApp\Services\MessageSender;
use App\Domain\Contact\Models\Contact;
use App\Domain\Analytics\Services\AnalyticsService;
use Illuminate\Support\Facades\Log;
use Exception;

class SendBulkMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Campaign $campaign;
    protected array $contactIds;

    /**
     * Create a new job instance.
     */
    public function __construct(Campaign $campaign, array $contactIds)
    {
        $this->campaign = $campaign;
        $this->contactIds = $contactIds;
    }

    /**
     * Execute the job.
     */
    public function handle(MessageSender $messageSender, AnalyticsService $analyticsService): void
    {
        try {
            Log::info('Starting bulk message job', [
                'campaign_id' => $this->campaign->id,
                'contact_count' => count($this->contactIds),
            ]);

            $successCount = 0;
            $failureCount = 0;

            foreach ($this->contactIds as $contactId) {
                try {
                    $contact = Contact::find($contactId);
                    
                    if (!$contact || !$contact->is_active) {
                        Log::warning('Skipping inactive or deleted contact', [
                            'contact_id' => $contactId,
                        ]);
                        continue;
                    }

                    // Process message content with variables
                    $messageContent = $this->processMessageTemplate(
                        $this->campaign->template_content,
                        $contact
                    );

                    // Send message
                    $result = $messageSender->sendMessage(
                        $this->campaign->whatsappAccount,
                        $contact->phone_number,
                        $messageContent,
                        $this->campaign->id
                    );

                    if ($result['success']) {
                        $successCount++;
                        
                        // Update campaign stats
                        $this->campaign->increment('messages_sent');
                        
                        // Track analytics
                        $analyticsService->recordUserActivity(
                            $this->campaign->user,
                            'message_sent',
                            1,
                            [
                                'campaign_id' => $this->campaign->id,
                                'contact_id' => $contact->id,
                            ]
                        );
                    } else {
                        $failureCount++;
                        $this->campaign->increment('messages_failed');
                        
                        Log::warning('Failed to send message', [
                            'campaign_id' => $this->campaign->id,
                            'contact_id' => $contact->id,
                            'error' => $result['error'] ?? 'Unknown error',
                        ]);
                    }

                    // Add delay between messages to avoid rate limiting
                    usleep(200000); // 200ms delay

                } catch (Exception $e) {
                    $failureCount++;
                    $this->campaign->increment('messages_failed');
                    
                    Log::error('Error sending message to contact', [
                        'campaign_id' => $this->campaign->id,
                        'contact_id' => $contactId,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // Update campaign status if all messages processed
            if ($this->campaign->messages_sent + $this->campaign->messages_failed >= $this->campaign->total_contacts) {
                $this->campaign->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);
            }

            Log::info('Bulk message job completed', [
                'campaign_id' => $this->campaign->id,
                'success_count' => $successCount,
                'failure_count' => $failureCount,
            ]);

        } catch (Exception $e) {
            Log::error('Bulk message job failed', [
                'campaign_id' => $this->campaign->id,
                'error' => $e->getMessage(),
            ]);

            // Mark campaign as failed
            $this->campaign->update(['status' => 'failed']);
            
            throw $e;
        }
    }

    /**
     * Process message template with contact variables
     */
    private function processMessageTemplate(string $template, Contact $contact): string
    {
        $variables = [
            '{{name}}' => $contact->name ?: 'Customer',
            '{{phone}}' => $contact->phone_number,
            '{{first_name}}' => $this->getFirstName($contact->name),
        ];

        return str_replace(array_keys($variables), array_values($variables), $template);
    }

    /**
     * Extract first name from full name
     */
    private function getFirstName(?string $fullName): string
    {
        if (!$fullName) {
            return 'Customer';
        }

        $parts = explode(' ', trim($fullName));
        return $parts[0];
    }

    /**
     * The job failed to process.
     */
    public function failed(Exception $exception): void
    {
        Log::error('SendBulkMessageJob failed', [
            'campaign_id' => $this->campaign->id,
            'error' => $exception->getMessage(),
        ]);

        // Mark campaign as failed
        $this->campaign->update(['status' => 'failed']);
    }
}
