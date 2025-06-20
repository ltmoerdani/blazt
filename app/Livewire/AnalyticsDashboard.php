<?php

namespace App\Livewire;

use App\Domain\Analytics\Models\CampaignAnalytic;
use App\Domain\Analytics\Models\UserAnalytic;
use App\Domain\Campaign\Models\Campaign;
use App\Domain\Contact\Models\Contact;
use App\Domain\WhatsApp\Models\WhatsAppAccount;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AnalyticsDashboard extends Component
{
    public $dateRange = '7days';
    public $selectedAccount = 'all';
    public $refreshInterval = 30; // seconds
    
    public function mount()
    {
        $this->loadData();
    }
    
    public function updatedDateRange()
    {
        $this->loadData();
    }
    
    public function updatedSelectedAccount()
    {
        $this->loadData();
    }
    
    public function refreshData()
    {
        $this->loadData();
        $this->dispatch('dataRefreshed');
    }
    
    private function loadData()
    {
        // This method will be called when filters change
        $this->dispatch('chartDataUpdated', [
            'metrics' => $this->getMetrics(),
            'chartData' => $this->getChartData()
        ]);
    }
    
    public function getMetrics()
    {
        $startDate = $this->getStartDate();
        $userId = Auth::id();
        
        // Get campaign analytics through campaigns owned by user
        $campaignIds = Campaign::where('user_id', $userId)->pluck('id');
        
        $query = CampaignAnalytic::whereIn('campaign_id', $campaignIds)
                                ->where('created_at', '>=', $startDate);
        
        $totalSent = $query->sum('messages_sent');
        $totalDelivered = $query->sum('messages_delivered');
        $totalFailed = $query->sum('messages_failed');
        
        return [
            'total_messages' => $totalSent,
            'successful_messages' => $totalDelivered,
            'failed_messages' => $totalFailed,
            'delivery_rate' => $totalSent > 0 ? round(($totalDelivered / $totalSent) * 100, 2) : 0,
            'active_campaigns' => $this->getActiveCampaigns(),
            'total_contacts' => $this->getTotalContacts(),
        ];
    }
    
    public function getChartData()
    {
        $startDate = $this->getStartDate();
        $userId = Auth::id();
        
        $campaignIds = Campaign::where('user_id', $userId)->pluck('id');
        
        $data = CampaignAnalytic::whereIn('campaign_id', $campaignIds)
                               ->where('created_at', '>=', $startDate)
                               ->selectRaw('DATE(created_at) as date, SUM(messages_sent) as sent, SUM(messages_delivered) as delivered')
                               ->groupBy('date')
                               ->orderBy('date')
                               ->get();
                        
        return [
            'labels' => $data->pluck('date')->map(fn($date) => Carbon::parse($date)->format('M d')),
            'sent' => $data->pluck('sent'),
            'delivered' => $data->pluck('delivered'),
        ];
    }
    
    private function getStartDate()
    {
        return match($this->dateRange) {
            '24hours' => Carbon::now()->subDay(),
            '7days' => Carbon::now()->subWeek(),
            '30days' => Carbon::now()->subMonth(),
            '90days' => Carbon::now()->subMonths(3),
            default => Carbon::now()->subWeek(),
        };
    }
    
    private function getActiveCampaigns()
    {
        return Campaign::where('user_id', Auth::id())
                      ->whereIn('status', ['running', 'scheduled'])
                      ->count();
    }
    
    private function getTotalContacts()
    {
        return Contact::where('user_id', Auth::id())->count();
    }
    
    public function render()
    {
        return view('livewire.analytics-dashboard', [
            'metrics' => $this->getMetrics(),
            'whatsappAccounts' => WhatsAppAccount::where('user_id', Auth::id())->get(),
            'recentCampaigns' => Campaign::where('user_id', Auth::id())
                                       ->latest()
                                       ->take(5)
                                       ->get(),
        ]);
    }
}
