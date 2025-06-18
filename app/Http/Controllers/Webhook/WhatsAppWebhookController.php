<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Domain\WhatsApp\Models\WhatsAppAccount;
use App\Domain\WhatsApp\Models\WhatsAppMessage;
use App\Domain\Contact\Models\Contact;
use App\Domain\AI\Services\ChatbotService;
use App\Domain\WhatsApp\Services\SessionManager;
use Exception;

class WhatsAppWebhookController extends Controller
{
    private const MISSING_DATA_MESSAGE = 'Missing data';

    protected $chatbotService;
    protected $sessionManager;

    public function __construct(ChatbotService $chatbotService, SessionManager $sessionManager)
    {
        $this->chatbotService = $chatbotService;
        $this->sessionManager = $sessionManager;
    }

    public function handleIncomingMessage(Request $request)
    {
        Log::info('Incoming WhatsApp Webhook:', $request->all());

        // Example: Basic validation for a known webhook structure
        // This part needs to be highly customized based on the actual WhatsApp API/library webhook format (e.g., Baileys)

        $phoneNumber = $request->input('from'); // Assuming 'from' field for sender
        $messageContent = $request->input('message'); // Assuming 'message' field for content
        $accountId = $request->input('whatsapp_account_id'); // Assuming a field to link to your WhatsAppAccount

        $response = null;

        if (empty($phoneNumber) || empty($messageContent) || empty($accountId)) {
            Log::warning('Webhook received with missing data.', $request->all());
            $response = response()->json(['status' => 'error', 'message' => self::MISSING_DATA_MESSAGE], 400);
        }

        if (!$response) {
            try {
                $whatsappAccount = WhatsAppAccount::find($accountId);
                if (!$whatsappAccount) {
                    Log::warning('WhatsApp account not found for webhook: ' . $accountId);
                    $response = response()->json(['status' => 'error', 'message' => 'Account not found'], 404);
                } else {
                    // Find or create contact based on phone number
                    $contact = Contact::firstOrCreate(
                        ['phone_number' => $phoneNumber, 'user_id' => $whatsappAccount->user_id],
                        ['name' => $phoneNumber] // Default name if not provided by webhook
                    );

                    // Store the incoming message
                    WhatsAppMessage::create([
                        'user_id' => $whatsappAccount->user_id,
                        'whatsapp_account_id' => $whatsappAccount->id,
                        'contact_id' => $contact->id,
                        'phone_number' => $phoneNumber,
                        'message_content' => $messageContent,
                        'message_type' => 'text', // Or infer from webhook data
                        'status' => 'received',
                        'sent_at' => now(), // Assuming received_at is sent_at from external system
                    ]);

                    // Process message with chatbot
                    // This part assumes a conversation context and AI response generation
                    $conversation = $contact->conversations()
                                            ->where('whatsapp_account_id', $whatsappAccount->id)
                                            ->where('status', 'active')
                                            ->latest('last_message_at')
                                            ->first();

                    if (!$conversation) {
                        // Start a new conversation if none exists or active one is found
                        $conversation = $this->chatbotService->startConversation($whatsappAccount->user, $whatsappAccount, $contact, $messageContent);
                    }

                    // Process the message within the conversation context
                    $this->chatbotService->processChatMessage($conversation, $messageContent, 'contact');

                    $response = response()->json(['status' => 'success'], 200);
                }
            } catch (Exception $e) {
                Log::error('Error processing WhatsApp webhook: ' . $e->getMessage());
                $response = response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
            }
        }

        return $response;
    }

    public function handleStatusUpdate(Request $request)
    {
        Log::info('Incoming WhatsApp Status Webhook:', $request->all());

        // Example: Update message status (delivered, read, failed)
        $messageId = $request->input('message_id');
        $newStatus = $request->input('status');
        $error = $request->input('error');

        $response = null;

        if (empty($messageId) || empty($newStatus)) {
            Log::warning('Status webhook received with missing data.', $request->all());
            $response = response()->json(['status' => 'error', 'message' => self::MISSING_DATA_MESSAGE], 400);
        }

        if (!$response) {
            try {
                $message = WhatsAppMessage::find($messageId);
                if ($message) {
                    $message->update([
                        'status' => $newStatus,
                        'error_message' => $error,
                        'delivered_at' => ($newStatus === 'delivered') ? now() : $message->delivered_at,
                        'read_at' => ($newStatus === 'read') ? now() : $message->read_at,
                    ]);
                    Log::info(sprintf('Message %s status updated to %s.', $messageId, $newStatus));
                    $response = response()->json(['status' => 'success'], 200);
                } else {
                    Log::warning('Message not found for status update: ' . $messageId);
                    $response = response()->json(['status' => 'error', 'message' => 'Message not found'], 404);
                }
            } catch (Exception $e) {
                Log::error('Error processing WhatsApp status webhook: ' . $e->getMessage());
                $response = response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
            }
        }
        return $response;
    }

    public function handleSessionUpdate(Request $request)
    {
        Log::info('Incoming WhatsApp Session Webhook:', $request->all());

        // Example: Update WhatsApp account session status (connected, disconnected, qr code)
        $accountId = $request->input('whatsapp_account_id');
        $status = $request->input('status'); // e.g., 'connected', 'disconnected', 'qr_code', 'pairing'
        $qrCodeData = $request->input('qr_code_data'); // Base64 image data or URL
        $sessionId = $request->input('session_id');

        $response = null;

        if (empty($accountId) || empty($status)) {
            Log::warning('Session webhook received with missing data.', $request->all());
            $response = response()->json(['status' => 'error', 'message' => self::MISSING_DATA_MESSAGE], 400);
        }

        if (!$response) {
            try {
                $whatsappAccount = WhatsAppAccount::find($accountId);
                if (!$whatsappAccount) {
                    Log::warning('WhatsApp account not found for session update: ' . $accountId);
                    $response = response()->json(['status' => 'error', 'message' => 'Account not found'], 404);
                } else {
                    if ($status === 'qr_code' && $qrCodeData) {
                        // Update QR code path in WhatsAppAccount model
                        // This logic might be handled by QRCodeGenerator service
                        $whatsappAccount->update(['qr_code_path' => $qrCodeData, 'status' => 'connecting']);
                        $this->sessionManager->createSession($whatsappAccount, $sessionId, $qrCodeData); // Create/update session for QR
                    } elseif ($status === 'connected') {
                        $whatsappAccount->update(['status' => 'connected', 'last_connected_at' => now()]);
                        $this->sessionManager->updateSession($whatsappAccount->sessions()->latest()->first(), ['status' => 'active', 'last_activity_at' => now()]);
                    } elseif ($status === 'disconnected' || $status === 'banned') {
                        $this->sessionManager->terminateSession($whatsappAccount);
                    }

                    Log::info(sprintf('WhatsApp account %s session status updated to %s.', $accountId, $status));
                    $response = response()->json(['status' => 'success'], 200);
                }
            } catch (Exception $e) {
                Log::error('Error processing WhatsApp session webhook: ' . $e->getMessage());
                $response = response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
            }
        }
        return $response;
    }
}
