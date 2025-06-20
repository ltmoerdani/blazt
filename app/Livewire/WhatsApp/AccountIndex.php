<?php

namespace App\Livewire\WhatsApp;

use Livewire\Component;
use App\Domain\WhatsApp\Models\WhatsAppAccount;
use App\Services\EnhancedWhatsAppService;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;

#[Title('WhatsApp Accounts - WhatsApp SaaS')]
class AccountIndex extends Component
{
    public $accounts = [];
    public $showConnectModal = false;
    public $connectingAccountId = null;
    public $qrCode = null;
    public $connectionStatus = 'disconnected';
    
    protected $listeners = [
        'refreshAccounts' => 'loadAccounts',
        'qrCodeUpdated' => 'updateQrCode',
        'connectionStatusChanged' => 'updateConnectionStatus'
    ];
    
    public function mount()
    {
        $this->loadAccounts();
    }
    
    public function loadAccounts()
    {
        $this->accounts = WhatsAppAccount::where('user_id', Auth::id())
            ->latest()
            ->get();
    }
    
    public function showConnectModal()
    {
        $this->showConnectModal = true;
        $this->resetConnectionState();
    }
    
    public function hideConnectModal()
    {
        $this->showConnectModal = false;
        $this->resetConnectionState();
    }
    
    public function resetConnectionState()
    {
        $this->connectingAccountId = null;
        $this->qrCode = null;
        $this->connectionStatus = 'disconnected';
    }
    
    public function startConnection($accountId = null)
    {
        try {
            if (!$accountId) {
                // Create new account
                $account = WhatsAppAccount::create([
                    'user_id' => Auth::id(),
                    'name' => 'WhatsApp Account',
                    'status' => 'connecting',
                    'connected_at' => null,
                ]);
                $this->connectingAccountId = $account->id;
            } else {
                $this->connectingAccountId = $accountId;
                $account = WhatsAppAccount::find($accountId);
                $account->update(['status' => 'connecting']);
            }
            
            $this->connectionStatus = 'connecting';
            
            // Call WhatsApp service to generate QR code
            $whatsappService = app(EnhancedWhatsAppService::class);
            $result = $whatsappService->generateQRCode($this->connectingAccountId);
            
            if ($result['success']) {
                $this->qrCode = $result['qr_code'];
                $this->connectionStatus = 'qr_ready';
            } else {
                $this->addError('connection', $result['message']);
                $this->connectionStatus = 'error';
            }
            
        } catch (\Exception $e) {
            $this->addError('connection', 'Failed to start connection: ' . $e->getMessage());
            $this->connectionStatus = 'error';
        }
    }
    
    public function refreshQrCode()
    {
        if ($this->connectingAccountId) {
            $this->startConnection($this->connectingAccountId);
        }
    }
    
    public function disconnectAccount($accountId)
    {
        try {
            $account = WhatsAppAccount::find($accountId);
            
            if ($account && $account->user_id === Auth::id()) {
                // Call WhatsApp service to disconnect
                $whatsappService = app(EnhancedWhatsAppService::class);
                $result = $whatsappService->disconnect($accountId);
                
                $account->update([
                    'status' => 'disconnected',
                    'connected_at' => null,
                    'session_data' => null,
                ]);
                
                $this->loadAccounts();
                
                session()->flash('message', 'WhatsApp account disconnected successfully.');
            }
        } catch (\Exception $e) {
            $this->addError('disconnect', 'Failed to disconnect: ' . $e->getMessage());
        }
    }
    
    public function deleteAccount($accountId)
    {
        try {
            $account = WhatsAppAccount::find($accountId);
            
            if ($account && $account->user_id === Auth::id()) {
                // Disconnect first if connected
                if ($account->status === 'connected') {
                    $this->disconnectAccount($accountId);
                }
                
                $account->delete();
                $this->loadAccounts();
                
                session()->flash('message', 'WhatsApp account deleted successfully.');
            }
        } catch (\Exception $e) {
            $this->addError('delete', 'Failed to delete account: ' . $e->getMessage());
        }
    }
    
    public function updateQrCode($qrCode)
    {
        $this->qrCode = $qrCode;
    }
    
    public function updateConnectionStatus($status, $accountId = null)
    {
        $this->connectionStatus = $status;
        
        if ($status === 'connected' && $accountId) {
            $account = WhatsAppAccount::find($accountId);
            if ($account) {
                $account->update([
                    'status' => 'connected',
                    'connected_at' => now(),
                ]);
            }
            
            $this->loadAccounts();
            $this->hideConnectModal();
            
            session()->flash('message', 'WhatsApp account connected successfully!');
        }
    }
    
    public function render()
    {
        return view('livewire.whatsapp.account-index');
    }
}
