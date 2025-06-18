<?php

namespace App\Http\Controllers\Dashboard;

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
            return redirect('/login');
        }
        /** @var \App\Domain\User\Models\User $user */

        $campaigns = $user->campaigns()->paginate(10);
        return view('dashboard.campaigns.index', compact('campaigns'));
    }

    public function create()
    {
        return view('dashboard.campaigns.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect('/login');
        }
        /** @var \App\Domain\User\Models\User $user */

        $request->validate([
            'whatsapp_account_id' => 'required|exists:whatsapp_accounts,id,user_id,' . $user->id,
            'name' => 'required|string|max:255',
            'template_content' => 'required|string',
            'target_type' => 'required|in:all,group,custom',
            'target_group_id' => 'nullable|exists:contact_groups,id,user_id,' . $user->id,
            'scheduled_at' => 'nullable|date',
        ]);

        try {
            $campaign = $this->campaignService->createCampaign($user, $request->all());
            return redirect()->route('dashboard.campaigns.show', $campaign->id)->with('success', 'Campaign created successfully.');
        } catch (Exception $e) {
            Log::error('Error creating campaign: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Failed to create campaign: ' . $e->getMessage()]);
        }
    }

    public function show(Campaign $campaign)
    {
        if (Auth::id() !== $campaign->user_id) {
            abort(403);
        }
        return view('dashboard.campaigns.show', compact('campaign'));
    }

    public function edit(Campaign $campaign)
    {
        if (Auth::id() !== $campaign->user_id) {
            abort(403);
        }
        return view('dashboard.campaigns.edit', compact('campaign'));
    }

    public function update(Request $request, Campaign $campaign)
    {
        if (Auth::id() !== $campaign->user_id) {
            abort(403);
        }

        $user = Auth::user();
        /** @var \App\Domain\User\Models\User $user */

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'template_content' => 'sometimes|required|string',
            'target_type' => 'sometimes|required|in:all,group,custom',
            'target_group_id' => 'nullable|exists:contact_groups,id,user_id,' . $user->id,
            'scheduled_at' => 'nullable|date',
            'status' => 'sometimes|required|in:draft,scheduled,running,completed,failed',
        ]);

        try {
            $this->campaignService->updateCampaign($campaign, $request->all());
            return redirect()->route('dashboard.campaigns.show', $campaign->id)->with('success', 'Campaign updated successfully.');
        } catch (Exception $e) {
            Log::error('Error updating campaign: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Failed to update campaign: ' . $e->getMessage()]);
        }
    }

    public function destroy(Campaign $campaign)
    {
        if (Auth::id() !== $campaign->user_id) {
            abort(403);
        }

        try {
            $campaign->delete();
            return redirect()->route('dashboard.campaigns.index')->with('success', 'Campaign deleted successfully.');
        } catch (Exception $e) {
            Log::error('Error deleting campaign: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to delete campaign: ' . $e->getMessage()]);
        }
    }

    public function execute(Campaign $campaign)
    {
        if (Auth::id() !== $campaign->user_id) {
            abort(403);
        }

        try {
            $this->campaignService->executeCampaign($campaign);
            return redirect()->route('dashboard.campaigns.show', $campaign->id)->with('success', 'Campaign execution initiated.');
        } catch (Exception $e) {
            Log::error('Error executing campaign: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to execute campaign: ' . $e->getMessage()]);
        }
    }
}

