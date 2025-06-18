<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Interfaces\Campaign\CampaignServiceInterface;
use App\Domain\User\Models\User;
use App\Domain\Campaign\Models\Campaign;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class CampaignController extends Controller
{
    protected $campaignService;

    public function __construct(CampaignServiceInterface $campaignService)
    {
        $this->campaignService = $campaignService;
    }

    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        /** @var \App\Domain\User\Models\User $user */

        $campaigns = $user->campaigns()->paginate(10);
        return response()->json($campaigns, 200);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        /** @var \App\Domain\User\Models\User $user */

        $request->validate([
            'whatsapp_account_id' => 'required|exists:whatsapp_accounts,id',
            'name' => 'required|string|max:255',
            'template_content' => 'required|string',
            'target_type' => 'required|in:all,group,custom',
            'target_group_id' => 'nullable|exists:contact_groups,id',
            'scheduled_at' => 'nullable|date',
        ]);

        try {
            $campaign = $this->campaignService->createCampaign($user, $request->all());
            return response()->json(['message' => 'Campaign created successfully.', 'data' => $campaign], 201);
        } catch (Exception $e) {
            Log::error('Error creating campaign: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to create campaign.', 'error' => $e->getMessage()], 500);
        }
    }

    public function show(Campaign $campaign)
    {
        if (Auth::id() !== $campaign->user_id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        return response()->json($campaign, 200);
    }

    public function update(Request $request, Campaign $campaign)
    {
        if (Auth::id() !== $campaign->user_id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'template_content' => 'sometimes|required|string',
            'target_type' => 'sometimes|required|in:all,group,custom',
            'target_group_id' => 'nullable|exists:contact_groups,id',
            'scheduled_at' => 'nullable|date',
            'status' => 'sometimes|required|in:draft,scheduled,running,completed,failed',
        ]);

        try {
            $this->campaignService->updateCampaign($campaign, $request->all());
            return response()->json(['message' => 'Campaign updated successfully.', 'data' => $campaign], 200);
        } catch (Exception $e) {
            Log::error('Error updating campaign: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to update campaign.', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Campaign $campaign)
    {
        if (Auth::id() !== $campaign->user_id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        try {
            $campaign->delete();
            return response()->json(['message' => 'Campaign deleted successfully.'], 204);
        } catch (Exception $e) {
            Log::error('Error deleting campaign: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to delete campaign.', 'error' => $e->getMessage()], 500);
        }
    }

    public function execute(Campaign $campaign)
    {
        if (Auth::id() !== $campaign->user_id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        try {
            $this->campaignService->executeCampaign($campaign);
            return response()->json(['message' => 'Campaign execution initiated.'], 200);
        } catch (Exception $e) {
            Log::error('Error executing campaign: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to execute campaign.', 'error' => $e->getMessage()], 500);
        }
    }
}

