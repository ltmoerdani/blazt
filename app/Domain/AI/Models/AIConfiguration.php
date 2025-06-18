<?php

namespace App\Domain\AI\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Domain\User\Models\User;

class AIConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'provider',
        'api_key',
        'model',
        'temperature',
        'max_tokens',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
