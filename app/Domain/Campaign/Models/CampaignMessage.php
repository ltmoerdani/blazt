<?php

namespace App\Domain\Campaign\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaignMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'message_content',
        'message_type',
        'order',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}
