<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class EnhancedWhatsAppService
{
    private string $baseUrl;
    private int $timeout;
    
    public function __construct()
    {
        $this->baseUrl = config('whatsapp.enhanced_handler_url', 'http://localhost:3001');
        $this->timeout = config('whatsapp.api_timeout', 30);
    }

    /**
     * Connect WhatsApp account
     */
    public function connectAccount(string $accountId, string $phoneNumber): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->post("{$this->baseUrl}/connect-account", [
                    'accountId' => $accountId,
                    'phoneNumber' => $phoneNumber,
                ]);

            if ($response->successful()) {
                Log::info('Enhanced WhatsApp connection initiated', [
                    'account_id' => $accountId,
                    'phone_number' => $phoneNumber
                ]);
                
                return $response->json();
            }

            throw new \Exception('Failed to connect account: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('Enhanced WhatsApp connection failed', [
                'account_id' => $accountId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Disconnect WhatsApp account
     */
    public function disconnectAccount(string $accountId): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->post("{$this->baseUrl}/disconnect-account", [
                    'accountId' => $accountId,
                ]);

            if ($response->successful()) {
                Log::info('Enhanced WhatsApp disconnection successful', [
                    'account_id' => $accountId
                ]);
                
                return $response->json();
            }

            throw new \Exception('Failed to disconnect account: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('Enhanced WhatsApp disconnection failed', [
                'account_id' => $accountId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get QR code for account
     */
    public function getQRCode(string $accountId): ?string
    {
        try {
            $response = Http::timeout($this->timeout)
                ->get("{$this->baseUrl}/qr-code/{$accountId}");

            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['success'] && isset($data['qr_code'])) {
                    return $data['qr_code'];
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Failed to get QR code from Enhanced Handler', [
                'account_id' => $accountId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Get connection status
     */
    public function getConnectionStatus(string $accountId): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->get("{$this->baseUrl}/status/{$accountId}");

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('Failed to get status: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('Failed to get status from Enhanced Handler', [
                'account_id' => $accountId,
                'error' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'connected' => false,
                'hasQR' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get all connections
     */
    public function getAllConnections(): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->get("{$this->baseUrl}/connections");

            if ($response->successful()) {
                return $response->json();
            }

            return ['success' => false, 'connections' => []];
        } catch (\Exception $e) {
            Log::error('Failed to get all connections from Enhanced Handler', [
                'error' => $e->getMessage()
            ]);
            return ['success' => false, 'connections' => []];
        }
    }

    /**
     * Check if Enhanced Handler is healthy
     */
    public function isHealthy(): bool
    {
        try {
            $response = Http::timeout(5)
                ->get("{$this->baseUrl}/health");

            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Refresh connection
     */
    public function refreshConnection(string $accountId, string $phoneNumber): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->post("{$this->baseUrl}/refresh-connection", [
                    'accountId' => $accountId,
                    'phoneNumber' => $phoneNumber,
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('Failed to refresh connection: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('Enhanced WhatsApp refresh failed', [
                'account_id' => $accountId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
