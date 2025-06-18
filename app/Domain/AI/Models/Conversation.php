<?php

namespace App\Domain\AI\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Domain\User\Models\User;
use App\Domain\WhatsApp\Models\WhatsAppAccount;
use App\Domain\Contact\Models\Contact;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'whatsapp_account_id',
        'contact_id',
        'status',
        'last_message_at',
        'ai_model_used',
        'context_data',
    ];

    protected $casts = [
        'context_data' => 'array',
        'last_message_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function whatsappAccount()
    {
        return $this->belongsTo(WhatsAppAccount::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function messages()
    {
        return $this->hasMany(ConversationMessage::class);
    }
}
