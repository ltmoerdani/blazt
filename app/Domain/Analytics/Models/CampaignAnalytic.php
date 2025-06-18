<?php

namespace App\Domain\Analytics\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Domain\Campaign\Models\Campaign;

class CampaignAnalytic extends Model
{
    use HasFactory;

    protected $table = 'campaign_analytics';

    protected $fillable = [
        'campaign_id',
        'total_contacts',
        'messages_queued',
        'messages_sent',
        'messages_delivered',
        'messages_read',
        'messages_failed',
        'delivery_rate',
        'read_rate',
        'avg_delivery_time',
        'last_updated_at',
    ];

    protected $casts = [
        'delivery_rate' => 'decimal:2',
        'read_rate' => 'decimal:2',
        'last_updated_at' => 'datetime',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}
