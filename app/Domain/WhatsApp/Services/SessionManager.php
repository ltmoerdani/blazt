<?php

namespace App\Domain\WhatsApp\Services;

use App\Domain\WhatsApp\Models\WhatsAppAccount;
use App\Domain\WhatsApp\Models\WhatsAppSession;
use App\Domain\WhatsApp\Exceptions\WhatsAppConnectionException;
use App\Interfaces\WhatsApp\SessionManagerInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Exception;

class SessionManager implements SessionManagerInterface
{
    public function createSession(WhatsAppAccount $account, string $sessionId, ?string $qrCodeData = null): WhatsAppSession
    {
        // Terminate any existing sessions for this account
        $this->terminateSession($account);

        // Create new session
        $session = WhatsAppSession::create([
            'whatsapp_account_id' => $account->id,
            'session_id' => $sessionId,
            'status' => WhatsAppSession::STATUS_CONNECTING,
            'qr_code' => $qrCodeData,
            'last_ping_at' => now(),
            'expires_at' => now()->addSeconds(config('whatsapp.session.qr_timeout')),
        ]);

        // Update account status
        $account->update([
            'status' => WhatsAppAccount::STATUS_CONNECTING,
            'qr_code_path' => $qrCodeData ? "qr-codes/{$account->id}.png" : null,
        ]);

        Log::info("WhatsApp session created for account: {$account->phone_number}", [
            'account_id' => $account->id,
            'session_id' => $sessionId,
        ]);

        return $session;
    }

    public function updateSession(WhatsAppSession $session, array $data): bool
    {
        $updated = $session->update($data);

        // Update account status based on session status
        if (isset($data['status'])) {
            $accountStatus = match ($data['status']) {
                WhatsAppSession::STATUS_CONNECTED => WhatsAppAccount::STATUS_CONNECTED,
                WhatsAppSession::STATUS_DISCONNECTED => WhatsAppAccount::STATUS_DISCONNECTED,
                WhatsAppSession::STATUS_FAILED => WhatsAppAccount::STATUS_DISCONNECTED,
                default => $session->whatsappAccount->status,
            };

            $session->whatsappAccount->update(['status' => $accountStatus]);

            if ($data['status'] === WhatsAppSession::STATUS_CONNECTED) {
                $session->whatsappAccount->markAsConnected();
            }
        }

        Log::info("WhatsApp session updated", [
            'session_id' => $session->session_id,
            'account_id' => $session->whatsapp_account_id,
            'data' => $data,
        ]);

        return $updated;
    }

    public function terminateSession(WhatsAppAccount $account): bool
    {
        try {
            // Update all active sessions for this account
            $activeSessions = $account->sessions()
                ->whereIn('status', [
                    WhatsAppSession::STATUS_CONNECTING,
                    WhatsAppSession::STATUS_CONNECTED
                ])
                ->get();

            foreach ($activeSessions as $session) {
                $session->markAsDisconnected();
            }

            // Update account status
            $account->markAsDisconnected();

            // Notify Node.js service to disconnect
            $this->notifyNodeServiceDisconnect($account);

            Log::info("WhatsApp sessions terminated for account: {$account->phone_number}", [
                'account_id' => $account->id,
                'terminated_sessions' => $activeSessions->count(),
            ]);

            return true;
        } catch (Exception $e) {
            Log::error("Failed to terminate WhatsApp session", [
                'account_id' => $account->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function getAccountSession(WhatsAppAccount $account): ?WhatsAppSession
    {
        return $account->sessions()
            ->whereIn('status', [
                WhatsAppSession::STATUS_CONNECTING,
                WhatsAppSession::STATUS_CONNECTED
            ])
            ->latest()
            ->first();
    }

    /**
     * Initiate connection with Node.js service
     */
    public function initiateConnection(WhatsAppAccount $account): WhatsAppSession
    {
        try {
            // Generate unique session ID
            $sessionId = Str::uuid()->toString();

            // Create initial session
            $session = $this->createSession($account, $sessionId);

            // Call Node.js service to start connection
            $response = $this->callNodeService('connect-account', [
                'accountId' => $account->id,
                'phoneNumber' => $account->formatted_phone,
                'sessionId' => $sessionId,
            ]);

            if (!$response['success']) {
                $session->update(['status' => WhatsAppSession::STATUS_FAILED]);
                throw WhatsAppConnectionException::connectionFailed($response['error'] ?? 'Unknown error');
            }

            Log::info("WhatsApp connection initiated for account: {$account->phone_number}", [
                'account_id' => $account->id,
                'session_id' => $sessionId,
            ]);

            return $session;
        } catch (Exception $e) {
            Log::error("Failed to initiate WhatsApp connection", [
                'account_id' => $account->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Check session health and update status
     */
    public function checkSessionHealth(WhatsAppSession $session): bool
    {
        try {
            $response = $this->callNodeService("account-status/{$session->whatsapp_account_id}", [], 'GET');

            if ($response['connected'] ?? false) {
                $session->updatePing();
                return true;
            } else {
                $session->markAsDisconnected();
                return false;
            }
        } catch (Exception $e) {
            Log::warning("Session health check failed", [
                'session_id' => $session->session_id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Call Node.js service
     */
    private function callNodeService(string $endpoint, array $data = [], string $method = 'POST'): array
    {
        $baseUrl = config('whatsapp.node_service.api_endpoint');
        $timeout = config('whatsapp.node_service.timeout');

        $response = Http::timeout($timeout)
            ->retry(config('whatsapp.node_service.max_retries'), 1000)
            ->{strtolower($method)}("{$baseUrl}/{$endpoint}", $data);

        if (!$response->successful()) {
            throw WhatsAppConnectionException::nodeServiceFailed($response->body());
        }

        return $response->json();
    }

    /**
     * Notify Node.js service to disconnect account
     */
    private function notifyNodeServiceDisconnect(WhatsAppAccount $account): void
    {
        try {
            $this->callNodeService('disconnect-account', [
                'accountId' => $account->id,
            ]);
        } catch (Exception $e) {
            Log::warning("Failed to notify Node service of disconnection", [
                'account_id' => $account->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
