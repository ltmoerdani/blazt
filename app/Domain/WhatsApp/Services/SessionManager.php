<?php

namespace App\Domain\WhatsApp\Services;

use App\Domain\WhatsApp\Models\WhatsAppAccount;
use App\Domain\WhatsApp\Models\WhatsAppSession;
use App\Interfaces\WhatsApp\SessionManagerInterface;
use Illuminate\Support\Facades\Log;
use Exception;

class SessionManager implements SessionManagerInterface
{
    public function createSession(WhatsAppAccount $account, string $sessionId, ?string $qrCodeData = null): WhatsAppSession
    {
        // Logic to store WhatsApp session data
        $session = WhatsAppSession::create([
            'whatsapp_account_id' => $account->id,
            'session_id' => $sessionId,
            'status' => 'active',
            'qr_code_data' => $qrCodeData,
            'last_activity_at' => now(),
        ]);

        $account->update(['status' => 'connected', 'last_connected_at' => now()]);

        return $session;
    }

    public function updateSession(WhatsAppSession $session, array $data): bool
    {
        return $session->update($data);
    }

    public function terminateSession(WhatsAppAccount $account): bool
    {
        $account->update(['status' => 'disconnected']);
        $account->sessions()->update(['status' => 'inactive']);
        Log::info('WhatsApp session terminated for account: ' . $account->phone_number);
        return true;
    }

    public function initiateConnection(WhatsAppAccount $account): void
    {
        Log::info('Initiating WhatsApp connection for account: ' . $account->phone_number);
        // Here, you would typically trigger your Node.js/Baileys service
        // to start a new session and generate a QR code.
        // The Node.js service would then send a webhook back to the /webhook/whatsapp/session-update endpoint
        // with the QR code data or a 'connected' status.

        // For now, let's simulate updating the account status to 'connecting'
        $account->update(['status' => 'connecting', 'qr_code_path' => null, 'session_data' => null]);

        // Example: If you directly generate QR code here (less common for external services)
        // $qrCodeData = $this->qrCodeGenerator->generateQrCodeForAccount($account->phone_number);
        // $account->update(['qr_code_path' => $qrCodeData]);
    }

    public function getAccountSession(WhatsAppAccount $account): ?WhatsAppSession
    {
        return $account->sessions()->latest()->first();
    }
}
