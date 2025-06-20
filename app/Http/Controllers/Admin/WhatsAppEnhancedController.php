<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\EnhancedWhatsAppService;
use App\Domain\WhatsApp\Models\WhatsAppAccount;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class WhatsAppEnhancedController extends Controller
{
    private EnhancedWhatsAppService $enhancedService;

    public function __construct(EnhancedWhatsAppService $enhancedService)
    {
        $this->enhancedService = $enhancedService;
    }

    /**
     * Get QR code for account
     */
    public function getQRCode(string $accountId): JsonResponse
    {
        try {
            // Verify user owns this account
            $recordId = str_replace('account_', '', $accountId);
            WhatsAppAccount::where('id', $recordId)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $qrCode = $this->enhancedService->getQRCode($accountId);
            
            if ($qrCode) {
                return response()->json([
                    'success' => true,
                    'qr_code' => $qrCode
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'QR code not available. Please try connecting first.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Failed to get QR code for admin panel', [
                'account_id' => $accountId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve QR code: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get connection status for account
     */
    public function getStatus(string $accountId): JsonResponse
    {
        try {
            // Verify user owns this account
            $recordId = str_replace('account_', '', $accountId);
            WhatsAppAccount::where('id', $recordId)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $status = $this->enhancedService->getConnectionStatus($accountId);
            
            return response()->json($status);
        } catch (\Exception $e) {
            Log::error('Failed to get status for admin panel', [
                'account_id' => $accountId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'connected' => false,
                'hasQR' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update account status in database
     */
    public function updateStatus(Request $request, int $recordId): JsonResponse
    {
        try {
            $account = WhatsAppAccount::where('id', $recordId)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $newStatus = $request->input('status');
            
            $updateData = ['status' => $newStatus];
            
            if ($newStatus === 'connected') {
                $updateData['last_connected_at'] = now();
            }
            
            $updateData['health_check_at'] = now();

            $account->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update status from admin panel', [
                'record_id' => $recordId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get QR code specifically for admin panel modal
     */
    public function getQRCodeForAccount(int $recordId): JsonResponse
    {
        try {
            // Verify user owns this account
            $account = WhatsAppAccount::where('id', $recordId)
                ->where('user_id', Auth::id())
                ->first();
                
            if (!$account) {
                return $this->errorResponse('Account not found or access denied', 404);
            }

            // Try simple ID format first since that works in Enhanced Handler
            $accountId = (string)$recordId;
            
            // Check if service is healthy
            $healthy = $this->enhancedService->isHealthy();
            if (!$healthy) {
                return $this->errorResponse('Enhanced Handler service is not available', 503);
            }

            // Sync status from Enhanced Handler to database
            $this->enhancedService->syncAccountStatus($accountId);
            
            // Get current status from Enhanced Handler
            $status = $this->enhancedService->getConnectionStatus($accountId);
            
            // Check database status as well
            $account->refresh(); // Refresh from database
            
            // If either Enhanced Handler or database shows connected, consider it connected
            if (($status && $status['connected']) || $account->status === 'connected') {
                // Update database if Enhanced Handler shows connected but DB doesn't
                if ($status && $status['connected'] && $account->status !== 'connected') {
                    $account->update([
                        'status' => 'connected',
                        'last_connected_at' => now(),
                        'health_check_at' => now()
                    ]);
                }
                
                return response()->json([
                    'success' => true,
                    'connected' => true,
                    'status' => $status,
                    'message' => 'Account is already connected'
                ]);
            }

            // Try to get QR code
            $qrCode = $this->enhancedService->getQRCode($accountId);
            
            if ($qrCode) {
                return response()->json([
                    'success' => true,
                    'connected' => false,
                    'qr_code' => $qrCode,
                    'status' => $status,
                    'account_id' => $accountId
                ]);
            }

            // If no QR code, try to initiate connection
            $connectResult = $this->enhancedService->connectAccount($accountId, $recordId);
            
            if ($connectResult['success']) {
                // Wait a moment for QR generation
                sleep(2);
                
                $qrCode = $this->enhancedService->getQRCode($accountId);
                if ($qrCode) {
                    return response()->json([
                        'success' => true,
                        'connected' => false,
                        'qr_code' => $qrCode,
                        'status' => $this->enhancedService->getConnectionStatus($accountId),
                        'account_id' => $accountId
                    ]);
                }
            }

            return $this->errorResponse('Unable to generate QR code. Please try again.', 400);

        } catch (\Exception $e) {
            Log::error('Failed to get QR code for admin account', [
                'record_id' => $recordId,
                'error' => $e->getMessage()
            ]);

            return $this->errorResponse('Failed to load QR code: ' . $e->getMessage(), 500);
        }
    }

    private function errorResponse(string $message, int $status = 400, array $debug = []): JsonResponse
    {
        $response = [
            'success' => false,
            'error' => $message
        ];

        if (!empty($debug)) {
            $response['debug'] = $debug;
        }

        return response()->json($response, $status);
    }
}
