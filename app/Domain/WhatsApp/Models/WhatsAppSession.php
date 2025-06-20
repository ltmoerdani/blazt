<?php

namespace App\Domain\WhatsApp\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsAppSession extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_sessions';

    protected $fillable = [
        'whatsapp_account_id',
        'session_id',
        'status',
        'qr_code',
        'session_data',
        'last_ping_at',
        'connected_at',
        'expires_at',
    ];

    protected $casts = [
        'session_data' => 'array',
        'last_ping_at' => 'datetime',
        'connected_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    // Session status constants
    public const STATUS_CONNECTING = 'connecting';
    public const STATUS_CONNECTED = 'connected';
    public const STATUS_DISCONNECTED = 'disconnected';
    public const STATUS_FAILED = 'failed';

    /**
     * Relationship with WhatsApp Account
     */
    public function whatsappAccount()
    {
        return $this->belongsTo(WhatsAppAccount::class);
    }

    /**
     * Check if session is active
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_CONNECTED &&
               $this->expires_at &&
               $this->expires_at->isFuture();
    }

    /**
     * Check if session is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Mark session as connected
     */
    public function markAsConnected(): self
    {
        $this->update([
            'status' => self::STATUS_CONNECTED,
            'connected_at' => now(),
            'expires_at' => now()->addSeconds(config('whatsapp.session.session_timeout')),
            'qr_code' => null, // Clear QR code when connected
        ]);

        return $this;
    }

    /**
     * Update last ping timestamp
     */
    public function updatePing(): self
    {
        $this->update([
            'last_ping_at' => now(),
            'expires_at' => now()->addSeconds(config('whatsapp.session.session_timeout')),
        ]);

        return $this;
    }

    /**
     * Mark session as disconnected
     */
    public function markAsDisconnected(): self
    {
        $this->update([
            'status' => self::STATUS_DISCONNECTED,
            'qr_code' => null,
            'expires_at' => null,
        ]);

        return $this;
    }
}
