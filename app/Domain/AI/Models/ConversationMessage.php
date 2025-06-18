<?php

namespace App\Domain\AI\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Domain\User\Models\User;

class ConversationMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_type',
        'message_content',
        'message_type',
        'timestamp',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }
}
