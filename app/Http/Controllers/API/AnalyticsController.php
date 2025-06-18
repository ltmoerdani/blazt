<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Interfaces\Analytics\AnalyticsServiceInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class AnalyticsController extends Controller
{
    protected $analyticsService;

    public function __construct(AnalyticsServiceInterface $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    public function getDashboardSummary(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        /** @var \App\Domain\User\Models\User $user */

        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        try {
            $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->subDays(30);
            $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now();

            $summary = $this->analyticsService->getDashboardSummary($user, $startDate, $endDate);
            return response()->json($summary, 200);
        } catch (Exception $e) {
            Log::error('Error fetching dashboard summary: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to fetch dashboard summary.', 'error' => $e->getMessage()], 500);
        }
    }

    public function getCampaignPerformance(Request $request, int $campaignId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        /** @var \App\Domain\User\Models\User $user */

        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        try {
            // Ensure the campaign belongs to the authenticated user
            $campaign = $user->campaigns()->findOrFail($campaignId);

            $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->subDays(30);
            $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now();

            $performance = $this->analyticsService->getCampaignPerformanceOverTime($campaign, $startDate, $endDate);
            return response()->json($performance, 200);
        } catch (Exception $e) {
            Log::error('Error fetching campaign performance: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to fetch campaign performance.', 'error' => $e->getMessage()], 500);
        }
    }
}

