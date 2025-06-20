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

    // Status constants
    public const STATUS_DISCONNECTED = 'disconnected';
    public const STATUS_CONNECTING = 'connecting';
    public const STATUS_CONNECTED = 'connected';
    public const STATUS_BANNED = 'banned';

    /**
     * Relationship with user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship with WhatsApp sessions
     */
    public function sessions()
    {
        return $this->hasMany(WhatsAppSession::class, 'whatsapp_account_id');
    }

    /**
     * Get current active session
     */
    public function currentSession()
    {
        return $this->hasOne(WhatsAppSession::class, 'whatsapp_account_id')
            ->where('status', WhatsAppSession::STATUS_CONNECTED)
            ->latest();
    }

    /**
     * Check if account is connected
     */
    public function isConnected(): bool
    {
        return $this->status === self::STATUS_CONNECTED;
    }

    /**
     * Check if account needs QR code
     */
    public function needsQRCode(): bool
    {
        return $this->status === self::STATUS_CONNECTING;
    }

    /**
     * Get formatted phone number for WhatsApp
     */
    public function getFormattedPhoneAttribute(): string
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/\D/', '', $this->phone_number);
        
        // Add country code if not present
        if (!str_starts_with($phone, '62') && strlen($phone) >= 10) {
            $phone = '62' . ltrim($phone, '0');
        }
        
        return $phone;
    }

    /**
     * Mark account as connected
     */
    public function markAsConnected(): self
    {
        $this->update([
            'status' => self::STATUS_CONNECTED,
            'last_connected_at' => now(),
            'qr_code_path' => null,
        ]);

        return $this;
    }

    /**
     * Mark account as disconnected
     */
    public function markAsDisconnected(): self
    {
        $this->update([
            'status' => self::STATUS_DISCONNECTED,
            'qr_code_path' => null,
        ]);

        return $this;
    }
}
