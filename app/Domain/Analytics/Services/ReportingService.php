<?php

namespace App\Domain\Analytics\Services;

use App\Domain\User\Models\User;
use App\Domain\Campaign\Models\Campaign;
use App\Domain\Analytics\Models\UserAnalytic;
use App\Domain\Analytics\Models\CampaignAnalytic;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReportingService
{
    protected $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    public function generateUserActivityReport(User $user, Carbon $startDate, Carbon $endDate): Collection
    {
        return UserAnalytic::where('user_id', $user->id)
            ->whereBetween('happened_at', [$startDate, $endDate])
            ->orderBy('happened_at')
            ->get();
    }

    public function generateCampaignReport(Campaign $campaign, Carbon $startDate, Carbon $endDate): array
    {
        $performanceOverTime = $this->analyticsService->getCampaignPerformanceOverTime($campaign, $startDate, $endDate);
        $campaignAnalytic = CampaignAnalytic::where('campaign_id', $campaign->id)->first();

        return [
            'campaign_details' => $campaign->toArray(),
            'overall_stats' => $campaignAnalytic ? $campaignAnalytic->toArray() : [],
            'performance_over_time' => $performanceOverTime,
        ];
    }

    public function generateOverallDashboardReport(User $user, Carbon $startDate, Carbon $endDate): array
    {
        $summary = $this->analyticsService->getDashboardSummary($user, $startDate, $endDate);

        // You can add more complex aggregation or data presentation here
        $topCampaigns = $user->campaigns()
            ->with('campaignAnalytic')
            ->orderByDesc('messages_sent')
            ->limit(5)
            ->get();

        return array_merge($summary, [
            'top_campaigns' => $topCampaigns->toArray(),
            // ... other aggregated reports
        ]);
    }

    // Method to export reports to CSV/Excel (example, requires a package like Laravel Excel)
    public function exportReportToCsv(Collection $data, string $filename): void
    {
        // Example: (requires laravel-excel package)
        // Excel::download(new YourExportClass($data), $filename . '.csv');
        // For now, simply log that an export would happen
        Log::info("Simulating CSV export for '{$filename}.csv' with " . $data->count() . " rows.");
    }
}
