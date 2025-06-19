<?php

namespace App\Filament\Resources\WhatsAppAccountResource\Pages;

use App\Filament\Resources\WhatsAppAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWhatsAppAccounts extends ListRecords
{
    protected static string $resource = WhatsAppAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
