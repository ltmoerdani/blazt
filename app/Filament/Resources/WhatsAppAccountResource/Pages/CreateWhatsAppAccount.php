<?php

namespace App\Filament\Resources\WhatsAppAccountResource\Pages;

use App\Filament\Resources\WhatsAppAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateWhatsAppAccount extends CreateRecord
{
    protected static string $resource = WhatsAppAccountResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        $data['status'] = 'disconnected';
        
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
