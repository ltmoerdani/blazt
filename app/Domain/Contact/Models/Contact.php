<?php

namespace App\Domain\Contact\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Domain\User\Models\User;
use App\Domain\Contact\Models\ContactGroup;
use App\Domain\WhatsApp\Models\WhatsAppMessage;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'phone_number',
        'name',
        'group_id',
        'is_active',
        'last_message_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_message_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function group()
    {
        return $this->belongsTo(ContactGroup::class, 'group_id');
    }

    public function messages()
    {
        return $this->hasMany(WhatsAppMessage::class);
    }
}
