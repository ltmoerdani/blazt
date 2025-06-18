<?php

namespace App\Domain\WhatsApp\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsAppSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'whatsapp_account_id',
        'session_id',
        'status',
        'last_activity_at',
        'qr_code_data',
    ];

    protected $casts = [
        'last_activity_at' => 'datetime',
    ];

    public function whatsappAccount()
    {
        return $this->belongsTo(WhatsAppAccount::class);
    }
}
