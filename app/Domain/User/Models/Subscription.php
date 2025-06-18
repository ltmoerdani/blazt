<?php

namespace App\Domain\User\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Domain\User\Models\User;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_name',
        'stripe_price_id',
        'stripe_subscription_id',
        'paddle_subscription_id',
        'status',
        'renews_at',
        'ends_at',
        'trial_ends_at',
    ];

    protected $casts = [
        'renews_at' => 'datetime',
        'ends_at' => 'datetime',
        'trial_ends_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
