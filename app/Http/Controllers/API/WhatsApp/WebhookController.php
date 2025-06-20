<?php

namespace App\Http\Controllers\Api\WhatsApp;

use App\Http\Controllers\Controller;
use App\Domain\WhatsApp\Models\WhatsAppAccount;
use App\Domain\WhatsApp\Models\WhatsAppSession;
use App\Interfaces\WhatsApp\SessionManagerInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class WebhookController extends Controller
{
    protected SessionManagerInterface $sessionManager;

    // Validation constants
    private const REQUIRED_ACCOUNT_ID = 'required|integer|exists:whatsapp_accounts,id';
    private const REQUIRED_STRING = 'required|string';
    private const NULLABLE_STRING = 'nullable|string';
    
    // Response constants
    private const ERROR_INVALID_DATA = 'Invalid data';
    private const ERROR_INTERNAL = 'Internal error';

    public function __construct(SessionManagerInterface $sessionManager)
    {
        $this->sessionManager = $sessionManager;
    }

    /**
     * Handle QR code generated webhook
     */
    public function qrGenerated(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'account_id' => self::REQUIRED_ACCOUNT_ID,
            'qr_code' => self::REQUIRED_STRING,
            'session_id' => self::REQUIRED_STRING,
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => self::ERROR_INVALID_DATA], 400);
        }

        try {
            $account = WhatsAppAccount::findOrFail($request->account_id);
            $session = $this->sessionManager->getAccountSession($account);

            if (!$session) {
                // Create new session if doesn't exist
                $session = $this->sessionManager->createSession(
                    $account,
                    $request->session_id,
                    $request->qr_code
                );
            } else {
                // Update existing session with QR code
                $this->sessionManager->updateSession($session, [
                    'qr_code' => $request->qr_code,
                    'status' => WhatsAppSession::STATUS_CONNECTING,
                    'expires_at' => now()->addSeconds(config('whatsapp.session.qr_timeout')),
                ]);
            }

            // Save QR code file for frontend access
            $this->saveQRCodeFile($account, $request->qr_code);

            Log::info("QR code generated for WhatsApp account", [
                'account_id' => $account->id,
                'session_id' => $request->session_id,
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error("Failed to process QR code webhook", [
                'account_id' => $request->account_id,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => self::ERROR_INTERNAL], 500);
        }
    }

    /**
     * Handle session status update webhook
     */
    public function sessionStatus(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'account_id' => self::REQUIRED_ACCOUNT_ID,
            'status' => self::REQUIRED_STRING . '|in:connecting,connected,disconnected,failed',
            'session_id' => self::NULLABLE_STRING,
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => self::ERROR_INVALID_DATA], 400);
        }

        try {
            $account = WhatsAppAccount::findOrFail($request->account_id);
            $session = $this->sessionManager->getAccountSession($account);

            if (!$session && $request->session_id) {
                // Create new session if doesn't exist
                $session = $this->sessionManager->createSession(
                    $account,
                    $request->session_id
                );
            }

            if ($session) {
                $updateData = ['status' => $request->status];

                if ($request->status === 'connected') {
                    $updateData['connected_at'] = now();
                    $updateData['qr_code'] = null; // Clear QR code when connected
                    $updateData['expires_at'] = now()->addSeconds(config('whatsapp.session.session_timeout'));
                }

                $this->sessionManager->updateSession($session, $updateData);
            }

            Log::info("Session status updated for WhatsApp account", [
                'account_id' => $account->id,
                'status' => $request->status,
                'session_id' => $request->session_id,
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error("Failed to process session status webhook", [
                'account_id' => $request->account_id,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Internal error'], 500);
        }
    }

    /**
     * Handle message status update webhook
     */
    public function messageStatus(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'message_id' => 'required|integer|exists:whatsapp_messages,id',
            'status' => 'required|string|in:sent,delivered,read,failed',
            'whatsapp_message_id' => 'nullable|string',
            'error_message' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid data'], 400);
        }

        try {
            $message = \App\Domain\WhatsApp\Models\WhatsAppMessage::findOrFail($request->message_id);
            
            $updateData = [
                'status' => $request->status,
                'updated_at' => now(),
            ];

            if ($request->whatsapp_message_id) {
                $updateData['whatsapp_message_id'] = $request->whatsapp_message_id;
            }

            if ($request->status === 'delivered') {
                $updateData['delivered_at'] = now();
            } elseif ($request->status === 'read') {
                $updateData['read_at'] = now();
            } elseif ($request->status === 'failed') {
                $updateData['error_message'] = $request->error_message;
            }

            $message->update($updateData);

            Log::info("Message status updated", [
                'message_id' => $message->id,
                'status' => $request->status,
                'whatsapp_message_id' => $request->whatsapp_message_id,
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error("Failed to process message status webhook", [
                'message_id' => $request->message_id,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Internal error'], 500);
        }
    }

    /**
     * Handle incoming message webhook
     */
    public function messageReceived(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'account_id' => 'required|integer|exists:whatsapp_accounts,id',
            'from' => 'required|string',
            'message_content' => 'required|string',
            'message_type' => 'required|string|in:text,image,video,audio,document',
            'media_url' => 'nullable|string',
            'whatsapp_message_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid data'], 400);
        }

        try {
            $account = WhatsAppAccount::findOrFail($request->account_id);
            
            // Create or get contact
            $contact = \App\Domain\Contact\Models\Contact::firstOrCreate(
                [
                    'user_id' => $account->user_id,
                    'phone_number' => $request->from,
                ],
                [
                    'name' => $request->from,
                    'is_active' => true,
                ]
            );

            // Create incoming message record
            $message = \App\Domain\WhatsApp\Models\WhatsAppMessage::create([
                'user_id' => $account->user_id,
                'whatsapp_account_id' => $account->id,
                'contact_id' => $contact->id,
                'phone_number' => $request->from,
                'message_content' => $request->message_content,
                'message_type' => $request->message_type,
                'media_url' => $request->media_url,
                'direction' => 'incoming',
                'status' => 'received',
                'whatsapp_message_id' => $request->whatsapp_message_id,
                'received_at' => now(),
            ]);

            Log::info("Incoming message received", [
                'account_id' => $account->id,
                'contact_id' => $contact->id,
                'message_id' => $message->id,
                'from' => $request->from,
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error("Failed to process incoming message webhook", [
                'account_id' => $request->account_id,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Internal error'], 500);
        }
    }

    /**
     * Save QR code as PNG file for frontend access
     */
    private function saveQRCodeFile(WhatsAppAccount $account, string $qrCodeData): void
    {
        try {
            $qrDir = storage_path('app/public/qr-codes');
            if (!file_exists($qrDir)) {
                mkdir($qrDir, 0755, true);
            }

            $qrCodePath = "{$qrDir}/{$account->id}.png";
            
            // If QR code is base64 data URL, extract base64 data
            if (str_starts_with($qrCodeData, 'data:image/png;base64,')) {
                $base64Data = str_replace('data:image/png;base64,', '', $qrCodeData);
                file_put_contents($qrCodePath, base64_decode($base64Data));
            } else {
                // Assume it's raw base64 data
                file_put_contents($qrCodePath, base64_decode($qrCodeData));
            }

            $account->update(['qr_code_path' => "qr-codes/{$account->id}.png"]);
        } catch (\Exception $e) {
            Log::warning("Failed to save QR code file", [
                'account_id' => $account->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
