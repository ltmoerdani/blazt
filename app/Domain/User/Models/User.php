<?php

namespace App\Domain\User\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Domain\WhatsApp\Models\WhatsAppAccount;
use App\Domain\WhatsApp\Models\WhatsAppMessage;
use App\Domain\Campaign\Models\Campaign;
use App\Domain\Contact\Models\Contact;
use App\Domain\Contact\Models\ContactGroup;
use App\Domain\AI\Models\Conversation;
use App\Domain\AI\Models\AIConfiguration;
use App\Domain\Analytics\Models\CampaignAnalytic;
use App\Domain\Analytics\Models\UserAnalytic;
use App\Domain\User\Models\Subscription;
use App\Domain\User\Models\UsageLog;
use App\Domain\User\Models\UserLimit;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'uuid',
        'name',
        'email',
        'password',
        'subscription_plan',
        'subscription_status',
        'subscription_expires_at',
        'timezone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'subscription_expires_at' => 'datetime',
    ];

    public function whatsappAccounts()
    {
        return $this->hasMany(WhatsAppAccount::class);
    }

    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function contactGroups()
    {
        return $this->hasMany(ContactGroup::class);
    }

    public function messages()
    {
        return $this->hasMany(WhatsAppMessage::class, 'user_id');
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    public function aiConfigurations()
    {
        return $this->hasMany(AIConfiguration::class);
    }

    public function campaignAnalytics()
    {
        return $this->hasMany(CampaignAnalytic::class);
    }

    public function userAnalytics()
    {
        return $this->hasMany(UserAnalytic::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function usageLogs()
    {
        return $this->hasMany(UsageLog::class);
    }

    public function userLimit()
    {
        return $this->hasOne(UserLimit::class);
    }
}
