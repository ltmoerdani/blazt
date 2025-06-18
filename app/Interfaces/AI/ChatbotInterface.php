<?php

namespace App\Interfaces\AI;

use App\Domain\User\Models\User;
use App\Domain\AI\Models\Conversation;
use App\Domain\AI\Models\ConversationMessage;
use App\Domain\Contact\Models\Contact;
use App\Domain\WhatsApp\Models\WhatsAppAccount;

interface ChatbotInterface
{
    public function startConversation(User $user, WhatsAppAccount $whatsappAccount, Contact $contact, string $initialMessage): Conversation;
    public function processChatMessage(Conversation $conversation, string $messageContent, string $senderType = 'contact'): ConversationMessage;
    public function updateConversationStatus(Conversation $conversation, string $status): bool;
} 