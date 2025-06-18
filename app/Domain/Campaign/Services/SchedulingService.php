<?php

namespace App\Domain\Campaign\Services;

use App\Domain\Campaign\Models\Campaign;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SchedulingService
{
    private const CAMPAIGN_LOG_PREFIX = 'Campaign ';

    public function scheduleCampaign(Campaign $campaign, Carbon $scheduledTime): bool
    {
        if ($campaign->status !== 'draft') {
            Log::warning(self::CAMPAIGN_LOG_PREFIX . $campaign->id . ' cannot be scheduled in its current status: ' . $campaign->status);
            return false;
        }

        $campaign->update([
            'status' => 'scheduled',
            'scheduled_at' => $scheduledTime,
        ]);

        // Dispatch a job to run the campaign at the scheduled time.
        // Example: RunCampaignJob::dispatch($campaign)->delay($scheduledTime);

        Log::info(self::CAMPAIGN_LOG_PREFIX . $campaign->id . ' scheduled for: ' . $scheduledTime->toDateTimeString());
        return true;
    }

    public function cancelScheduledCampaign(Campaign $campaign): bool
    {
        if ($campaign->status !== 'scheduled') {
            Log::warning(self::CAMPAIGN_LOG_PREFIX . $campaign->id . ' is not scheduled and cannot be cancelled.');
            return false;
        }

        $campaign->update([
            'status' => 'draft',
            'scheduled_at' => null,
        ]);

        // Cancel any pending jobs related to this campaign

        Log::info('Scheduled ' . strtolower(self::CAMPAIGN_LOG_PREFIX) . $campaign->id . ' cancelled.');
        return true;
    }

    public function rescheduleCampaign(Campaign $campaign, Carbon $newScheduledTime): bool
    {
        // First, cancel the existing schedule
        $this->cancelScheduledCampaign($campaign);

        // Then, schedule with the new time
        return $this->scheduleCampaign($campaign, $newScheduledTime);
    }

    public function getUpcomingScheduledCampaigns(int $limit = 10)
    {
        return Campaign::where('status', 'scheduled')
            ->where('scheduled_at', '>', now())
            ->orderBy('scheduled_at')
            ->limit($limit)
            ->get();
    }
}
