<?php

namespace App\Domain\Campaign\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Domain\User\Models\User;
use App\Domain\WhatsApp\Models\WhatsAppAccount;
use App\Domain\Contact\Models\ContactGroup;
use App\Domain\WhatsApp\Models\WhatsAppMessage;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'whatsapp_account_id',
        'name',
        'template_content',
        'target_type',
        'target_group_id',
        'status',
        'scheduled_at',
        'started_at',
        'completed_at',
        'total_contacts',
        'messages_sent',
        'messages_delivered',
        'messages_failed',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function whatsappAccount()
    {
        return $this->belongsTo(WhatsAppAccount::class);
    }

    public function targetGroup()
    {
        return $this->belongsTo(ContactGroup::class, 'target_group_id');
    }

    public function messages()
    {
        return $this->hasMany(WhatsAppMessage::class);
    }
}
