<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * Handle basic dashboard functionality for authenticated users
 */
class DashboardController extends Controller
{
    /**
     * Show the application dashboard.
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        // Basic dashboard data for Phase 1
        $dashboardData = [
            'user' => $user,
            'subscription_status' => $user->subscription_status,
            'subscription_plan' => $user->subscription_plan,
            'subscription_expires_at' => $user->subscription_expires_at,
            'email_verified' => $user->hasVerifiedEmail(),
            'total_contacts' => 0, // Will be implemented in Contact phase
            'total_campaigns' => 0, // Will be implemented in Campaign phase
            'total_messages' => 0, // Will be implemented in Messaging phase
        ];

        return view('dashboard.index', compact('dashboardData'));
    }
}
