<?php

namespace App\Interfaces\Analytics;

use App\Domain\User\Models\User;
use App\Domain\Campaign\Models\Campaign;
use App\Domain\Analytics\Models\UserAnalytic;
use Carbon\Carbon;
use Illuminate\Support\Collection;

interface AnalyticsServiceInterface
{
    public function recordUserActivity(User $user, string $activityType, int $quantity = 1, array $metadata = []): UserAnalytic;
    public function updateCampaignMetrics(Campaign $campaign): void;
    public function getDashboardSummary(User $user, Carbon $startDate, Carbon $endDate): array;
    public function getCampaignPerformanceOverTime(Campaign $campaign, Carbon $startDate, Carbon $endDate): array;
}