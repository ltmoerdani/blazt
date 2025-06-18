<?php

namespace App\Interfaces\WhatsApp;

use App\Domain\User\Models\User;
use App\Domain\WhatsApp\Models\WhatsAppAccount;
use App\Domain\WhatsApp\Models\WhatsAppMessage;
use App\Domain\Contact\Models\Contact;

interface WhatsAppServiceInterface
{
    public function connectAccount(User $user, string $phoneNumber, string $displayName = null): WhatsAppAccount;
    public function disconnectAccount(WhatsAppAccount $account): bool;
    public function getQRCode(WhatsAppAccount $account): ?string;
    public function sendMessage(WhatsAppAccount $account, Contact $contact, string $messageContent, ?string $mediaPath = null): WhatsAppMessage;
    public function getAccountStatus(WhatsAppAccount $account): string;
} 