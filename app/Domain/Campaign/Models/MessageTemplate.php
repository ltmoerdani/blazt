<?php

namespace App\Domain\Campaign\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Domain\User\Models\User;

class MessageTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'content',
        'category',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
