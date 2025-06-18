<?php

namespace App\Domain\WhatsApp\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Domain\User\Models\User;

class WhatsAppAccount extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_accounts';

    protected $fillable = [
        'user_id',
        'phone_number',
        'display_name',
        'status',
        'session_data',
        'qr_code_path',
        'last_connected_at',
        'health_check_at',
    ];

    protected $casts = [
        'session_data' => 'array',
        'last_connected_at' => 'datetime',
        'health_check_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
