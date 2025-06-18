<?php

namespace App\Domain\WhatsApp\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Domain\User\Models\User;
use App\Domain\Campaign\Models\Campaign;
use App\Domain\Contact\Models\Contact;

class WhatsAppMessage extends Model
{
    use HasFactory;

    protected $table = 'messages'; // Explicitly set table name as it's pluralized differently

    protected $fillable = [
        'user_id',
        'campaign_id',
        'whatsapp_account_id',
        'contact_id',
        'phone_number',
        'message_content',
        'media_path',
        'status',
        'error_message',
        'sent_at',
        'delivered_at',
        'read_at',
        'created_year_month',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function whatsappAccount()
    {
        return $this->belongsTo(WhatsAppAccount::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    // For partitioned table, we might need to override newQuery or use global scopes
    // to include the partitioning key in queries if not always provided.
    protected static function booted()
    {
        static::creating(function ($message) {
            if (!$message->created_year_month) {
                $message->created_year_month = now()->format('Ym');
            }
        });
    }
}
