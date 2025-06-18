<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Interfaces\Analytics\AnalyticsServiceInterface;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected $analyticsService;

    public function __construct(AnalyticsServiceInterface $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            // This should ideally be handled by middleware, but as a fallback:
            return redirect('/login'); // Redirect to login page or show error
        }

        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfDay();

        $summary = $this->analyticsService->getDashboardSummary($user, $startDate, $endDate);

        return view('dashboard.index', [
            'summary' => $summary,
            'user' => $user,
            // Pass other data needed for the dashboard view
        ]);
    }
}
