<?php

namespace App\Domain\Analytics\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Domain\User\Models\User;

class UserAnalytic extends Model
{
    use HasFactory;

    protected $table = 'daily_analytics';

    protected $fillable = [
        'user_id',
        'date',
        'messages_sent',
        'messages_delivered',
        'messages_failed',
        'campaigns_created',
        'campaigns_completed',
        'ai_requests_used',
        'unique_contacts_messaged',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
