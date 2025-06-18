<?php

namespace App\Domain\Contact\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Domain\User\Models\User;

class ContactGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'contact_count',
    ];

    protected $casts = [
        'contact_count' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class, 'group_id');
    }
}
