<?php

namespace App\Interfaces\WhatsApp;

use App\Domain\WhatsApp\Models\WhatsAppAccount;
use App\Domain\WhatsApp\Models\WhatsAppSession;

interface SessionManagerInterface
{
    public function createSession(WhatsAppAccount $account, string $sessionId, ?string $qrCodeData = null): WhatsAppSession;
    public function updateSession(WhatsAppSession $session, array $data): bool;
    public function terminateSession(WhatsAppAccount $account): bool;
    public function getAccountSession(WhatsAppAccount $account): ?WhatsAppSession;
}
