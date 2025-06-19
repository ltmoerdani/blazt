<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Contacts', $this->getTotalContacts())
                ->description('Total contacts in your database')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),
                
            Stat::make('WhatsApp Accounts', $this->getWhatsAppAccounts())
                ->description('Connected WhatsApp accounts')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('primary'),
                
            Stat::make('Total Campaigns', $this->getTotalCampaigns())
                ->description('Created campaigns')
                ->descriptionIcon('heroicon-m-megaphone')
                ->color('warning'),
                
            Stat::make('Messages Sent', $this->getMessagesSent())
                ->description('Total messages sent today')
                ->descriptionIcon('heroicon-m-paper-airplane')
                ->color('info'),
        ];
    }
    
    private function getTotalContacts(): int
    {
        // Phase 1: Return placeholder data (contacts feature in Task 4.1)
        return 0;
    }
    
    private function getWhatsAppAccounts(): int
    {
        // Phase 1: Return placeholder data (WhatsApp integration in Task 3.1)
        // When implemented: return WhatsAppAccount::where('user_id', auth()->id())->count();
        return Auth::check() ? 1 : 0; // Show 1 if logged in, 0 if not
    }
    
    private function getTotalCampaigns(): int
    {
        // Phase 1: Return placeholder data (campaigns in Phase 2)
        // When implemented: return Campaign::where('user_id', auth()->id())->count();
        $userId = Auth::id() ?? 0;
        return Cache::remember("campaigns_count_{$userId}", 300, fn() => 0);
    }
    
    private function getMessagesSent(): int
    {
        // Phase 1: Return placeholder data (messaging in Task 5.1)
        // When implemented: return Message::whereDate('created_at', today())->where('user_id', auth()->id())->count();
        return (int) now()->format('H'); // Return current hour as placeholder to show dynamic data
    }
}
