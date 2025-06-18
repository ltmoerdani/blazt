<?php

namespace App\Domain\User\Services;

use App\Domain\User\Models\User;
use App\Domain\User\Models\UsageLog;
use App\Domain\User\Models\UserLimit;
use Carbon\Carbon;
use Exception;

class UsageTrackingService
{
    public function recordUsage(User $user, string $usageType, int $quantity = 1, array $metadata = []): UsageLog
    {
        return UsageLog::create([
            'user_id' => $user->id,
            'usage_type' => $usageType,
            'quantity' => $quantity,
            'metadata' => $metadata,
            'created_at' => now(),
        ]);
    }

    public function checkUsageLimit(User $user, string $usageType, int $currentUsage = 0): bool
    {
        $limit = $this->getUserLimit($user);
        $allowed = true;

        if ($limit !== null) {
            // Example: Check against daily limit for 'messages' or 'ai_requests'
            if ($usageType === 'message' && isset($limit->messages_daily_limit)) {
                $dailyUsage = $user->usageLogs()
                    ->where('usage_type', 'message')
                    ->whereDate('created_at', Carbon::today())
                    ->sum('quantity');
                $allowed = ($dailyUsage + $currentUsage) <= $limit->messages_daily_limit;
            } elseif ($usageType === 'ai_request' && isset($limit->ai_requests_daily_limit)) {
                $dailyUsage = $user->usageLogs()
                    ->where('usage_type', 'ai_request')
                    ->whereDate('created_at', Carbon::today())
                    ->sum('quantity');
                $allowed = ($dailyUsage + $currentUsage) <= $limit->ai_requests_daily_limit;
            }
            // Add more specific usage type checks as needed (e.g., monthly limits, account limits)
        }

        return $allowed;
    }

    protected function getUserLimit(User $user): ?UserLimit
    {
        return $user->userLimit;
    }

    public function getDailyUsage(User $user, string $usageType): int
    {
        return $user->usageLogs()
            ->where('usage_type', $usageType)
            ->whereDate('created_at', Carbon::today())
            ->sum('quantity');
    }

    public function getMonthlyUsage(User $user, string $usageType): int
    {
        return $user->usageLogs()
            ->where('usage_type', $usageType)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('quantity');
    }
}
