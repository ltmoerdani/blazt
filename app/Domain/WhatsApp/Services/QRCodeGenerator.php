<?php

namespace App\Domain\WhatsApp\Services;

use App\Domain\WhatsApp\Models\WhatsAppAccount;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Exception;

class QRCodeGenerator
{
    public function generateAndSave(WhatsAppAccount $account, string $data): ?string
    {
        try {
            $filename = 'qr-codes/' . $account->id . '.svg';

            // Generate QR code and save to storage
            QrCode::format('svg')
                  ->size(200)
                  ->errorCorrection('H')
                  ->generate($data, Storage::path('public/' . $filename));

            $account->update(['qr_code_path' => $filename]);
            return $filename;
        } catch (Exception $e) {
            // Log the error
            report($e);
            return null;
        }
    }

    public function generate(WhatsAppAccount $account): string
    {
        // Generate a unique QR code path for the account
        return 'qr-codes/' . $account->id . '_' . now()->timestamp . '.svg';
    }

    public function generateQRCode(WhatsAppAccount $account): string
    {
        return $this->generate($account);
    }
}
