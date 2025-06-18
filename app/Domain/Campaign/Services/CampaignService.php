<?php

namespace App\Domain\Campaign\Services;

use App\Domain\User\Models\User;
use App\Domain\Campaign\Models\Campaign;
use App\Domain\Campaign\Models\MessageTemplate;
use App\Domain\Contact\Models\ContactGroup;
use App\Domain\WhatsApp\Services\MessageSender;
use App\Domain\WhatsApp\Models\WhatsAppAccount;
use App\Domain\Contact\Models\Contact;
use App\Interfaces\Campaign\CampaignServiceInterface;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CampaignService implements CampaignServiceInterface
{
    protected $messageSender;

    public function __construct(MessageSender $messageSender)
    {
        $this->messageSender = $messageSender;
    }

    public function createCampaign(User $user, array $data): Campaign
    {
        return DB::transaction(function () use ($user, $data) {
            return $user->campaigns()->create([
                'whatsapp_account_id' => $data['whatsapp_account_id'],
                'name' => $data['name'],
                'template_content' => $data['template_content'],
                'target_type' => $data['target_type'] ?? 'all',
                'target_group_id' => $data['target_type'] === 'group' ? $data['target_group_id'] : null,
                'status' => 'draft',
                'scheduled_at' => $data['scheduled_at'] ?? null,
            ]);
        });
    }

    public function updateCampaign(Campaign $campaign, array $data): bool
    {
        return DB::transaction(function () use ($campaign, $data) {
            return $campaign->update([
                'name' => $data['name'] ?? $campaign->name,
                'template_content' => $data['template_content'] ?? $campaign->template_content,
                'target_type' => $data['target_type'] ?? $campaign->target_type,
                'target_group_id' => $data['target_group_id'] ?? $campaign->target_group_id,
                'status' => $data['status'] ?? $campaign->status,
                'scheduled_at' => $data['scheduled_at'] ?? $campaign->scheduled_at,
            ]);
        });
    }

    public function executeCampaign(Campaign $campaign): bool
    {
        if ($campaign->status !== 'scheduled' && $campaign->status !== 'draft') {
            Log::warning('Campaign ' . $campaign->id . ' cannot be executed in current status: ' . $campaign->status);
            return false;
        }

        $campaign->update(['status' => 'running', 'started_at' => now()]);

        $contacts = $this->getCampaignTargetContacts($campaign);
        $campaign->update(['total_contacts' => $contacts->count()]);

        foreach ($contacts as $contact) {
            // Dispatch job to send message to each contact
            // Example: SendBulkMessageJob::dispatch($campaign, $contact);
            // For now, directly sending for simplicity
            try {
                $this->messageSender->send(
                    $campaign->messages()->create([
                        'user_id' => $campaign->user_id,
                        'whatsapp_account_id' => $campaign->whatsapp_account_id,
                        'contact_id' => $contact->id,
                        'phone_number' => $contact->phone_number,
                        'message_content' => $this->parseTemplate($campaign->template_content, $contact),
                        'status' => 'queued',
                    ])
                );
                $campaign->increment('messages_sent');
            } catch (Exception $e) {
                $campaign->increment('messages_failed');
                Log::error('Failed to send message for campaign ' . $campaign->id . ' to contact ' . $contact->id . ': ' . $e->getMessage());
            }
        }

        $campaign->update(['status' => 'completed', 'completed_at' => now()]);
        Log::info('Campaign ' . $campaign->id . ' completed.');

        return true;
    }

    public function deleteCampaign(Campaign $campaign): bool
    {
        return DB::transaction(function () use ($campaign) {
            // Delete associated messages
            $campaign->messages()->delete();

            // Delete the campaign
            return $campaign->delete();
        });
    }

    public function getCampaignStats(Campaign $campaign): array
    {
        return [
            'total_contacts' => $campaign->total_contacts,
            'messages_sent' => $campaign->messages_sent,
            'messages_delivered' => $campaign->messages_delivered,
            'messages_failed' => $campaign->messages_failed,
            'delivery_rate' => $campaign->messages_sent > 0 ? round(($campaign->messages_delivered / $campaign->messages_sent) * 100, 2) : 0,
            'status' => $campaign->status,
            'created_at' => $campaign->created_at,
            'started_at' => $campaign->started_at,
            'completed_at' => $campaign->completed_at,
        ];
    }

    protected function getCampaignTargetContacts(Campaign $campaign)
    {
        if ($campaign->target_type === 'all') {
            return $campaign->user->contacts()->where('is_active', true)->get();
        } elseif ($campaign->target_type === 'group') {
            return $campaign->targetGroup->contacts()->where('is_active', true)->get();
        } else {
            // Handle custom contact lists if applicable
            return collect();
        }
    }

    protected function parseTemplate(string $template, Contact $contact): string
    {
        // Simple template parsing for demonstration. Can be extended with Spintax, etc.
        return str_replace('{contact_name}', $contact->name ?? $contact->phone_number, $template);
    }
}
