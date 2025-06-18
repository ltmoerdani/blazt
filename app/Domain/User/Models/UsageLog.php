<?php

namespace App\Domain\User\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Domain\User\Models\User;

class UsageLog extends Model
{
    use HasFactory;

    protected $table = 'usage_logs';

    protected $fillable = [
        'user_id',
        'usage_type',
        'quantity',
        'metadata',
        'created_year_month',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted()
    {
        static::creating(function ($usageLog) {
            if (!$usageLog->created_year_month) {
                $usageLog->created_year_month = now()->format('Ym');
            }
        });
    }
}
