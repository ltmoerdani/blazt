<?php

namespace App\Domain\WhatsApp\Services;

use App\Domain\User\Models\User;
use App\Domain\WhatsApp\Models\WhatsAppAccount;
use App\Domain\WhatsApp\Models\WhatsAppMessage;
use App\Domain\WhatsApp\Repositories\WhatsAppAccountRepository;
use App\Domain\Contact\Models\Contact;
use App\Interfaces\WhatsApp\SessionManagerInterface;
use App\Interfaces\WhatsApp\MessageSenderInterface;
use App\Interfaces\WhatsApp\WhatsAppServiceInterface;
use App\Domain\WhatsApp\Services\QRCodeGenerator;
use Illuminate\Support\Facades\Log;
use Exception;

class WhatsAppService implements WhatsAppServiceInterface
{
    protected $whatsAppAccountRepository;
    protected $sessionManager;
    protected $messageSender;
    protected $qrCodeGenerator;

    public function __construct(
        WhatsAppAccountRepository $whatsAppAccountRepository,
        SessionManagerInterface $sessionManager,
        MessageSenderInterface $messageSender,
        QRCodeGenerator $qrCodeGenerator
    )
    {
        $this->whatsAppAccountRepository = $whatsAppAccountRepository;
        $this->sessionManager = $sessionManager;
        $this->messageSender = $messageSender;
        $this->qrCodeGenerator = $qrCodeGenerator;
    }

    public function createAccount(User $user, array $data): WhatsAppAccount
    {
        return $this->whatsAppAccountRepository->create([
            'user_id' => $user->id,
            'phone_number' => $data['phone_number'],
            'display_name' => $data['display_name'] ?? 'WhatsApp Account ' . $data['phone_number'],
            'status' => $data['status'] ?? 'disconnected',
            'session_data' => null,
            'qr_code_path' => null,
            'last_connected_at' => null,
            'health_check_at' => now(),
        ]);
    }

    public function updateAccount(WhatsAppAccount $account, array $data): bool
    {
        return $this->whatsAppAccountRepository->update($account, [
            'phone_number' => $data['phone_number'] ?? $account->phone_number,
            'display_name' => $data['display_name'] ?? $account->display_name,
            'status' => $data['status'] ?? $account->status,
        ]);
    }

    public function deleteAccount(WhatsAppAccount $account): bool
    {
        // Also terminate any active session associated with this account
        $this->sessionManager->terminateSession($account);
        return $this->whatsAppAccountRepository->delete($account);
    }

    public function connectAccount(User $user, string $phoneNumber, ?string $displayName = null): WhatsAppAccount
    {
        // Create or find existing account
        $whatsappAccount = $this->whatsAppAccountRepository->findByPhoneNumber($phoneNumber);
        
        if (!$whatsappAccount) {
            // Create new account
            $whatsappAccount = $this->createAccount($user, [
                'phone_number' => $phoneNumber,
                'display_name' => $displayName,
                'status' => 'connecting'
            ]);
        } elseif ($whatsappAccount->user_id !== $user->id) {
            throw new WhatsAppAccountUnauthorizedException('WhatsApp account unauthorized.');
        }

        // Generate QR code for connection
        $qrCodePath = $this->qrCodeGenerator->generateQRCode($whatsappAccount);
        $whatsappAccount->update([
            'status' => 'connecting',
            'qr_code_path' => $qrCodePath,
            'last_connected_at' => null
        ]);

        return $whatsappAccount;
    }

    public function disconnectAccount(WhatsAppAccount $account): bool
    {
        // Logic to disconnect from WhatsApp API and terminate session
        $this->sessionManager->terminateSession($account);
        $account->update(['status' => 'disconnected', 'qr_code_path' => null]);
        return true;
    }

    public function getQRCode(WhatsAppAccount $account): ?string
    {
        if ($account->status === 'connecting' && $account->qr_code_path) {
            return $account->qr_code_path;
        }
        
        // Generate new QR code if needed
        if ($account->status === 'disconnected') {
            $qrCodePath = $this->qrCodeGenerator->generate($account);
            $account->update(['qr_code_path' => $qrCodePath, 'status' => 'connecting']);
            return $qrCodePath;
        }
        
        return null;
    }

    public function sendMessage(WhatsAppAccount $account, Contact $contact, string $messageContent, ?string $mediaPath = null): WhatsAppMessage
    {
        // Use MessageSender service to send the message
        $response = $this->messageSender->sendMessage(
            $account,
            $contact->phone_number,
            $messageContent,
            null, // campaign_id
            $mediaPath ? 'media' : 'text'
        );

        // Return the message that was created by MessageSender
        return WhatsAppMessage::find($response['message_id']);
    }

    public function processIncomingMessage(array $messageData): WhatsAppMessage
    {
        // Logic to process incoming WhatsApp messages
        // This would involve parsing messageData from webhook and creating WhatsAppMessage

        // Example (simplified)
        $whatsappAccount = $this->whatsAppAccountRepository->findByPhoneNumber($messageData['to']);
        $user = $whatsappAccount->user;
        $contact = Contact::firstOrCreate(
            ['user_id' => $user->id, 'phone_number' => $messageData['from']],
            ['name' => $messageData['from']]
        );

        // Immediately return the created message
        return WhatsAppMessage::create([
            'user_id' => $user->id,
            'whatsapp_account_id' => $whatsappAccount->id,
            'contact_id' => $contact->id,
            'phone_number' => $messageData['from'], // Needs to be the contact's phone number or from messageData
            'message_content' => $messageData['content'],
            'status' => 'received',
        ]);
    }

    public function getAccountStatus(WhatsAppAccount $account): string
    {
        // This method would query the Node.js service for the real-time status
        // For now, return the stored status.
        return $account->status;
    }
}
