<?php

namespace App\Livewire;

use Livewire\Component;
use App\Domain\WhatsApp\Models\WhatsAppAccount;
use App\Domain\Campaign\Models\Campaign;
use App\Domain\Contact\Models\Contact;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;

#[Title('Dashboard - WhatsApp SaaS')]
class Dashboard extends Component
{
    public $stats = [];
    
    public function mount()
    {
        $this->loadStats();
    }
    
    protected function loadStats()
    {
        $user = Auth::user();
        
        $this->stats = [
            'total_accounts' => WhatsAppAccount::where('user_id', $user->id)->count(),
            'active_accounts' => WhatsAppAccount::where('user_id', $user->id)
                ->where('status', 'connected')->count(),
            'total_campaigns' => Campaign::where('user_id', $user->id)->count(),
            'total_contacts' => Contact::where('user_id', $user->id)->count(),
            'recent_campaigns' => Campaign::where('user_id', $user->id)
                ->latest()
                ->take(5)
                ->get(),
            'recent_accounts' => WhatsAppAccount::where('user_id', $user->id)
                ->latest()
                ->take(3)
                ->get(),
        ];
    }
    
    public function refreshStats()
    {
        $this->loadStats();
        
        $this->dispatch('notify', [
            'message' => 'Dashboard statistics refreshed successfully!',
            'type' => 'success'
        ]);
    }
    
    public function render()
    {
        return view('livewire.dashboard');
    }
}
