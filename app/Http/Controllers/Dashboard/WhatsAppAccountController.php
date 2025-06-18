<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\WhatsApp\Services\WhatsAppService;
use App\Domain\WhatsApp\Models\WhatsAppAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Domain\User\Models\User;

class WhatsAppAccountController extends Controller
{
    private const LOGIN_ROUTE = '/login';

    protected $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect(self::LOGIN_ROUTE);
        }

        $accounts = $user->whatsAppAccounts()->paginate(10);
        return view('dashboard.whatsapp.accounts.index', compact('accounts'));
    }

    public function create()
    {
        return view('dashboard.whatsapp.accounts.create');
    }

    public function store(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect(self::LOGIN_ROUTE);
        }

        $request->validate([
            'phone_number' => 'required|string|max:20|unique:whatsapp_accounts,phone_number,NULL,id,user_id,' . $user->id,
            'display_name' => 'nullable|string|max:255',
            'status' => 'in:disconnected,connecting,connected,banned',
        ]);

        $response = null;

        try {
            $account = $this->whatsAppService->createAccount($user, $request->all());
            $response = redirect()->route('dashboard.whatsapp.accounts.show', $account->id)->with('success', 'WhatsApp account created successfully.');
        } catch (Exception $e) {
            Log::error('Error creating WhatsApp account: ' . $e->getMessage());
            $response = back()->withInput()->withErrors(['error' => 'Failed to create WhatsApp account: ' . $e->getMessage()]);
        }

        return $response;
    }

    public function show(WhatsAppAccount $whatsAppAccount)
    {
        if (Auth::id() !== $whatsAppAccount->user_id) {
            abort(403);
        }
        return view('dashboard.whatsapp.accounts.show', compact('whatsAppAccount'));
    }

    public function edit(WhatsAppAccount $whatsAppAccount)
    {
        if (Auth::id() !== $whatsAppAccount->user_id) {
            abort(403);
        }
        return view('dashboard.whatsapp.accounts.edit', compact('whatsAppAccount'));
    }

    public function update(Request $request, WhatsAppAccount $whatsAppAccount)
    {
        if (Auth::id() !== $whatsAppAccount->user_id) {
            abort(403);
        }

        /** @var User $user */
        $user = Auth::user();

        $request->validate([
            'phone_number' => 'sometimes|required|string|max:20|unique:whatsapp_accounts,phone_number,' . $whatsAppAccount->id . ',id,user_id,' . $user->id,
            'display_name' => 'nullable|string|max:255',
            'status' => 'sometimes|required|in:disconnected,connecting,connected,banned',
        ]);

        $response = null;

        try {
            $this->whatsAppService->updateAccount($whatsAppAccount, $request->all());
            $response = redirect()->route('dashboard.whatsapp.accounts.show', $whatsAppAccount->id)->with('success', 'WhatsApp account updated successfully.');
        } catch (Exception $e) {
            Log::error('Error updating WhatsApp account: ' . $e->getMessage());
            $response = back()->withInput()->withErrors(['error' => 'Failed to update WhatsApp account: ' . $e->getMessage()]);
        }

        return $response;
    }

    public function destroy(WhatsAppAccount $whatsAppAccount)
    {
        if (Auth::id() !== $whatsAppAccount->user_id) {
            abort(403);
        }

        $response = null;

        try {
            $this->whatsAppService->deleteAccount($whatsAppAccount);
            $response = redirect()->route('dashboard.whatsapp.accounts.index')->with('success', 'WhatsApp account deleted successfully.');
        } catch (Exception $e) {
            Log::error('Error deleting WhatsApp account: ' . $e->getMessage());
            $response = back()->withErrors(['error' => 'Failed to delete WhatsApp account: ' . $e->getMessage()]);
        }

        return $response;
    }

    public function connect(WhatsAppAccount $whatsAppAccount)
    {
        if (Auth::id() !== $whatsAppAccount->user_id) {
            abort(403);
        }

        $response = null;

        try {
            $this->whatsAppService->connectAccount($whatsAppAccount->user, $whatsAppAccount->phone_number, $whatsAppAccount->display_name);
            $response = redirect()->route('dashboard.whatsapp.accounts.show', $whatsAppAccount->id)->with('success', 'WhatsApp account connection initiated. Check QR code.');
        } catch (Exception $e) {
            Log::error('Error initiating WhatsApp account connection: ' . $e->getMessage());
            $response = back()->withErrors(['error' => 'Failed to initiate connection: ' . $e->getMessage()]);
        }

        return $response;
    }

    public function disconnect(WhatsAppAccount $whatsAppAccount)
    {
        if (Auth::id() !== $whatsAppAccount->user_id) {
            abort(403);
        }

        $response = null;

        try {
            $this->whatsAppService->disconnectAccount($whatsAppAccount);
            $response = redirect()->route('dashboard.whatsapp.accounts.show', $whatsAppAccount->id)->with('success', 'WhatsApp account disconnected.');
        } catch (Exception $e) {
            Log::error('Error disconnecting WhatsApp account: ' . $e->getMessage());
            $response = back()->withErrors(['error' => 'Failed to disconnect account: ' . $e->getMessage()]);
        }

        return $response;
    }
}
