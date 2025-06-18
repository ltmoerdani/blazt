<?php

namespace App\Domain\WhatsApp\Repositories;

use App\Domain\WhatsApp\Models\WhatsAppAccount;
use App\Infrastructure\AbstractRepository;

class WhatsAppAccountRepository extends AbstractRepository
{
    protected function getModelClass(): string
    {
        return WhatsAppAccount::class;
    }

    public function findByPhoneNumber(string $phoneNumber): ?WhatsAppAccount
    {
        return WhatsAppAccount::where('phone_number', $phoneNumber)->first();
    }
}
