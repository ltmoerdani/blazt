<?php

namespace App\Domain\User\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserLimit extends Model
{
    protected $table = 'user_limits';

    protected $fillable = [
        'user_id',
        'messages_daily_limit',
        'messages_monthly_limit',
        'ai_requests_daily_limit',
        'whatsapp_accounts_limit',
        'contacts_limit',
        'campaigns_limit',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
