<?php

namespace App\Domain\Analytics\Services;

use App\Domain\User\Models\User;
use App\Domain\Campaign\Models\Campaign;
use App\Domain\Analytics\Models\UserAnalytic;
use App\Domain\Analytics\Models\CampaignAnalytic;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsService
{
    public function recordUserActivity(User $user, string $activityType, int $quantity = 1, array $metadata = []): UserAnalytic
    {
        // This method could be used to record various user actions like logins, feature usage, etc.
        return UserAnalytic::create([
            'user_id' => $user->id,
            'activity_type' => $activityType,
            'quantity' => $quantity,
            'metadata' => $metadata,
            'happened_at' => now(),
        ]);
    }

    public function updateCampaignMetrics(Campaign $campaign): void
    {
        // Recalculate and update campaign analytics based on messages associated with the campaign
        $totalMessages = $campaign->messages()->count();
        $sentMessages = $campaign->messages()->where('status', 'sent')->count();
        $deliveredMessages = $campaign->messages()->where('status', 'delivered')->count();
        $readMessages = $campaign->messages()->where('status', 'read')->count();
        $failedMessages = $campaign->messages()->where('status', 'failed')->count();

        $deliveryRate = $totalMessages > 0 ? ($deliveredMessages / $totalMessages) * 100 : 0;
        $readRate = $deliveredMessages > 0 ? ($readMessages / $deliveredMessages) * 100 : 0;

        CampaignAnalytic::updateOrCreate(
            ['campaign_id' => $campaign->id],
            [
                'total_contacts' => $campaign->total_contacts,
                'messages_queued' => $campaign->total_contacts - $sentMessages - $failedMessages, // Approximation
                'messages_sent' => $sentMessages,
                'messages_delivered' => $deliveredMessages,
                'messages_read' => $readMessages,
                'messages_failed' => $failedMessages,
                'delivery_rate' => $deliveryRate,
                'read_rate' => $readRate,
                // avg_delivery_time needs more complex logic, possibly from message timestamps
                'last_updated_at' => now(),
            ]
        );
    }

    public function getDashboardSummary(User $user, Carbon $startDate, Carbon $endDate): array
    {
        // Aggregate key metrics for the dashboard within a date range
        $messagesSent = $user->messages()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', ['sent', 'delivered', 'read'])
            ->count();

        $campaignsCompleted = $user->campaigns()
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->count();

        $activeWhatsAppAccounts = $user->whatsappAccounts()
            ->where('status', 'connected')
            ->count();

        // You can add more summary metrics here based on your analytics needs

        return [
            'messages_sent' => $messagesSent,
            'campaigns_completed' => $campaignsCompleted,
            'active_whatsapp_accounts' => $activeWhatsAppAccounts,
            'total_contacts' => $user->contacts()->count(),
            // ... other aggregated metrics
        ];
    }

    public function getCampaignPerformanceOverTime(Campaign $campaign, Carbon $startDate, Carbon $endDate): array
    {
        // Get daily performance data for a specific campaign
        return $campaign->messages()
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total_messages'),
                DB::raw('SUM(CASE WHEN status = "delivered" THEN 1 ELSE 0 END) as delivered'),
                DB::raw('SUM(CASE WHEN status = "read" THEN 1 ELSE 0 END) as read'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();
    }
}
