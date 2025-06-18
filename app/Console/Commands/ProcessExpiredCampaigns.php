<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Domain\Campaign\Models\Campaign;
use App\Domain\Campaign\Services\CampaignService;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class ProcessExpiredCampaigns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'campaigns:process-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Marks expired campaigns as completed or failed and logs their status.';

    protected $campaignService;

    public function __construct(CampaignService $campaignService)
    {
        parent::__construct();
        $this->campaignService = $campaignService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Processing expired campaigns...');

        $expiredCampaigns = Campaign::whereIn('status', ['scheduled', 'running'])
            ->where('scheduled_at', '<=', Carbon::now())
            ->get();

        if ($expiredCampaigns->isEmpty()) {
            $this->info('No expired campaigns to process.');
            return Command::SUCCESS;
        }

        foreach ($expiredCampaigns as $campaign) {
            try {
                // If campaign was scheduled but not started, mark as failed
                if ($campaign->status === 'scheduled') {
                    $campaign->update([
                        'status' => 'failed',
                        'completed_at' => Carbon::now(),
                        'messages_failed' => $campaign->total_contacts, // Assume all failed if not started
                    ]);
                    Log::warning('Campaign ' . $campaign->id . ' expired before starting. Marked as failed.');
                    $this->warn('Campaign ' . $campaign->id . ' expired before starting. Marked as failed.');
                } else if ($campaign->status === 'running') {
                    // If campaign was running, mark as completed
                    $campaign->update([
                        'status' => 'completed',
                        'completed_at' => Carbon::now(),
                    ]);
                    Log::info('Campaign ' . $campaign->id . ' completed.');
                    $this->info('Campaign ' . $campaign->id . ' completed.');
                }

                // You might also want to trigger analytics updates here
                // $this->campaignService->updateCampaignMetrics($campaign);

            } catch (Exception $e) {
                Log::error('Error processing expired campaign ' . $campaign->id . ': ' . $e->getMessage());
                $this->error('Error processing expired campaign ' . $campaign->id . ': ' . $e->getMessage());
            }
        }

        $this->info('Expired campaigns processing completed.');

        return Command::SUCCESS;
    }
} 