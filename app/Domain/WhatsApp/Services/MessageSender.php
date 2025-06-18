<?php

namespace App\Domain\WhatsApp\Services;

use App\Domain\WhatsApp\Models\WhatsAppMessage;
use App\Domain\WhatsApp\Models\WhatsAppAccount;
use App\Domain\Contact\Models\Contact;
use App\Interfaces\WhatsApp\MessageSenderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class MessageSendException extends \Exception {}

class MessageSender implements MessageSenderInterface
{
    public function send(WhatsAppMessage $message): bool
    {
        try {
            // Simulate sending message via an external Node.js Baileys service
            Log::info('Attempting to send WhatsApp message: ' . $message->id);
            // For now, simulate success
            $message->update(['status' => 'sent']);
            Log::info('Simulated WhatsApp message sent successfully: ' . $message->id);
            return true;
        } catch (\Exception $e) {
            $message->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
            Log::error('Error sending WhatsApp message: ' . $e->getMessage());
            throw new MessageSendException('Error sending WhatsApp message: ' . $e->getMessage());
        }
    }

    public function sendWhatsAppMessage(WhatsAppAccount $account, Contact $contact, string $messageContent, ?string $mediaPath = null): WhatsAppMessage
    {
        try {
            $message = WhatsAppMessage::create([
                'user_id' => $account->user_id,
                'whatsapp_account_id' => $account->id,
                'contact_id' => $contact->id,
                'phone_number' => $contact->phone_number,
                'message_content' => $messageContent,
                'media_path' => $mediaPath,
                'status' => 'queued',
            ]);

            $this->send($message);

            return $message;
        } catch (\Exception $e) {
            Log::error('Error in sendWhatsAppMessage: ' . $e->getMessage());
            throw new MessageSendException('Failed to prepare and send WhatsApp message: ' . $e->getMessage());
        }
    }

    public function sendMessage(
        WhatsAppAccount $account,
        string $phoneNumber,
        string $content,
        ?int $campaignId = null,
        string $messageType = 'text'
    ): array {
        try {
            // Create message record
            $message = WhatsAppMessage::create([
                'whatsapp_account_id' => $account->id,
                'user_id' => $account->user_id,
                'phone_number' => $phoneNumber,
                'message_content' => $content,
                'message_type' => $messageType,
                'direction' => 'outgoing',
                'status' => 'pending',
                'campaign_id' => $campaignId,
            ]);

            // Send via Node.js service
            $result = $this->sendViaNodeService($account, $phoneNumber, $content, $message->id);

            if ($result['success']) {
                $message->update([
                    'status' => 'sent',
                    'sent_at' => now(),
                ]);

                Log::info('Message sent successfully', [
                    'message_id' => $message->id,
                    'phone_number' => $phoneNumber,
                ]);

                return ['success' => true, 'message_id' => $message->id];
            } else {
                $message->update([
                    'status' => 'failed',
                    'error_message' => $result['error'],
                ]);

                return ['success' => false, 'error' => $result['error']];
            }

        } catch (Exception $e) {
            Log::error('Failed to send message', [
                'phone_number' => $phoneNumber,
                'error' => $e->getMessage(),
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    protected function sendViaNodeService(WhatsAppAccount $account, string $phoneNumber, string $content, int $messageId): array
    {
        try {
            $nodeServiceUrl = config('whatsapp.node_service.api_endpoint');

            $response = Http::timeout(30)->post("{$nodeServiceUrl}/send-message", [
                'account_id' => $account->id,
                'phone_number' => $phoneNumber,
                'message' => $content,
                'message_id' => $messageId,
            ]);

            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json()];
            } else {
                return ['success' => false, 'error' => $response->body()];
            }

        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
