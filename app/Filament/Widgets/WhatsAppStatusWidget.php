<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class WhatsAppStatusWidget extends Widget
{
    protected static string $view = 'filament.widgets.whatsapp-status';
    
    protected int | string | array $columnSpan = 'full';
    
    public function getWhatsAppStatus(): array
    {
        return [
            'connected_accounts' => 0, // Will be implemented in WhatsApp phase
            'total_accounts' => 0,
            'connection_status' => 'disconnected',
            'last_sync' => null,
        ];
    }
}
