<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Interfaces\WhatsApp\WhatsAppServiceInterface;
use App\Domain\User\Models\User;
use App\Domain\WhatsApp\Models\WhatsAppAccount;
use App\Domain\Contact\Models\Contact;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class WhatsAppController extends Controller
{
    protected $whatsAppService;

    public function __construct(WhatsAppServiceInterface $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    public function connectAccount(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string|max:20',
            'display_name' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $response = null;

        try {
            $account = $this->whatsAppService->connectAccount(
                $user,
                $request->phone_number,
                $request->display_name
            );

            $response = response()->json([
                'message' => 'WhatsApp account connection initiated.',
                'account' => $account,
                'qr_code_path' => $account->qr_code_path,
            ], 200);
        } catch (Exception $e) {
            Log::error('Error connecting WhatsApp account: ' . $e->getMessage());
            $response = response()->json(['message' => 'Failed to connect WhatsApp account.', 'error' => $e->getMessage()], 500);
        }

        return $response;
    }

    public function disconnectAccount(WhatsAppAccount $account)
    {
        // Ensure the authenticated user owns this WhatsApp account
        if (Auth::id() !== $account->user_id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $response = null;

        try {
            $this->whatsAppService->disconnectAccount($account);
            $response = response()->json(['message' => 'WhatsApp account disconnected successfully.'], 200);
        } catch (Exception $e) {
            Log::error('Error disconnecting WhatsApp account: ' . $e->getMessage());
            $response = response()->json(['message' => 'Failed to disconnect WhatsApp account.', 'error' => $e->getMessage()], 500);
        }

        return $response;
    }

    public function getQRCode(WhatsAppAccount $account)
    {
        // Ensure the authenticated user owns this WhatsApp account
        if (Auth::id() !== $account->user_id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $response = null;

        try {
            $qrCodePath = $this->whatsAppService->getQRCode($account);
            if ($qrCodePath) {
                $response = response()->json(['qr_code_path' => asset('storage/' . $qrCodePath)], 200);
            } else {
                $response = response()->json(['message' => 'QR code not available.'], 404);
            }
        } catch (Exception $e) {
            Log::error('Error retrieving QR code: ' . $e->getMessage());
            $response = response()->json(['message' => 'Failed to retrieve QR code.', 'error' => $e->getMessage()], 500);
        }

        return $response;
    }

    public function sendMessage(Request $request, WhatsAppAccount $account)
    {
        $request->validate([
            'contact_id' => 'required|exists:contacts,id',
            'message_content' => 'required|string',
            'media_path' => 'nullable|string',
        ]);

        // Ensure the authenticated user owns this WhatsApp account
        if (Auth::id() !== $account->user_id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $contact = Contact::find($request->contact_id);
        if (!$contact || $contact->user_id !== Auth::id()) {
            return response()->json(['message' => 'Contact not found or unauthorized.'], 404);
        }

        $response = null;

        try {
            $message = $this->whatsAppService->sendMessage(
                $account,
                $contact,
                $request->message_content,
                $request->media_path
            );

            $response = response()->json(['message' => 'Message sent successfully.', 'data' => $message], 200);
        } catch (Exception $e) {
            Log::error('Error sending message: ' . $e->getMessage());
            $response = response()->json(['message' => 'Failed to send message.', 'error' => $e->getMessage()], 500);
        }

        return $response;
    }

    public function getAccountStatus(WhatsAppAccount $account)
    {
        // Ensure the authenticated user owns this WhatsApp account
        if (Auth::id() !== $account->user_id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $response = null;

        try {
            $status = $this->whatsAppService->getAccountStatus($account);
            $response = response()->json(['status' => $status], 200);
        } catch (Exception $e) {
            Log::error('Error getting account status: ' . $e->getMessage());
            $response = response()->json(['message' => 'Failed to retrieve account status.', 'error' => $e->getMessage()], 500);
        }

        return $response;
    }
}
