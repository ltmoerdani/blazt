<?php

namespace App\Interfaces\WhatsApp;

use App\Domain\WhatsApp\Models\WhatsAppMessage;
use App\Domain\WhatsApp\Models\WhatsAppAccount;
use App\Domain\Contact\Models\Contact;

interface MessageSenderInterface
{
    public function send(WhatsAppMessage $message): bool;
    
    public function sendMessage(
        WhatsAppAccount $account,
        string $phoneNumber,
        string $content,
        ?int $campaignId = null,
        string $messageType = 'text'
    ): array;
    
    public function sendWhatsAppMessage(
        WhatsAppAccount $account,
        Contact $contact,
        string $messageContent,
        ?string $mediaPath = null
    ): WhatsAppMessage;
}
