<?php

namespace App\Domain\User\Services;

use App\Domain\User\Models\User;
use App\Domain\User\Models\Subscription;
use App\Domain\User\Models\UsageLog;
use App\Domain\User\Models\UserLimit;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Exception;
use Carbon\Carbon;

class UserService
{
    public function createUser(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'subscription_plan' => $data['subscription_plan'] ?? 'trial',
                'subscription_status' => 'active',
                'subscription_expires_at' => now()->addDays(7), // Default trial
            ]);

            // Create default limits for the new user
            $this->createDefaultUserLimits($user);

            return $user;
        });
    }

    public function updateUser(User $user, array $data): bool
    {
        return $user->update(array_filter([
            'name' => $data['name'] ?? null,
            'email' => $data['email'] ?? null,
            'password' => isset($data['password']) ? Hash::make($data['password']) : null,
            'timezone' => $data['timezone'] ?? null,
        ]));
    }

    public function deleteUser(User $user): ?bool
    {
        return $user->delete();
    }

    public function createDefaultUserLimits(User $user): UserLimit
    {
        return UserLimit::create([
            'user_id' => $user->id,
            'messages_daily_limit' => 1000,
            'messages_monthly_limit' => 30000,
            'ai_requests_daily_limit' => 100,
            'whatsapp_accounts_limit' => 1,
            'contacts_limit' => 10000,
            'campaigns_limit' => 50,
        ]);
    }

    public function getUserLimits(User $user): ?UserLimit
    {
        return $user->userLimit;
    }

    public function updateSubscription(User $user, string $plan, string $status, ?Carbon $expiresAt = null): bool
    {
        return $user->update([
            'subscription_plan' => $plan,
            'subscription_status' => $status,
            'subscription_expires_at' => $expiresAt,
        ]);
    }

    public function logUsage(User $user, string $usageType, int $quantity = 1, array $metadata = []): UsageLog
    {
        return UsageLog::create([
            'user_id' => $user->id,
            'usage_type' => $usageType,
            'quantity' => $quantity,
            'metadata' => $metadata,
        ]);
    }
}
