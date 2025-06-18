<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Domain\WhatsApp\Models\WhatsAppSession;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class CleanupOldSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sessions:cleanup-old';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleans up old or inactive WhatsApp sessions.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting cleanup of old WhatsApp sessions...');

        $threshold = Carbon::now()->subDays(30); // Sessions older than 30 days

        $oldSessions = WhatsAppSession::where('last_activity_at', '<=', $threshold)
                                     ->orWhere('status', 'inactive')
                                     ->orWhere('status', 'disconnected')
                                     ->get();

        if ($oldSessions->isEmpty()) {
            $this->info('No old or inactive sessions found to clean up.');
            return Command::SUCCESS;
        }

        $deletedCount = 0;
        foreach ($oldSessions as $session) {
            try {
                $session->delete();
                $deletedCount++;
            } catch (Exception $e) {
                Log::error('Error deleting old WhatsApp session ' . $session->id . ': ' . $e->getMessage());
                $this->error('Error deleting old WhatsApp session ' . $session->id . ': ' . $e->getMessage());
            }
        }

        $this->info(sprintf('Successfully cleaned up %d old WhatsApp sessions.', $deletedCount));
        Log::info(sprintf('Cleaned up %d old WhatsApp sessions.', $deletedCount));

        return Command::SUCCESS;
    }
} 