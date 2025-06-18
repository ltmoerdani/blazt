<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Domain\WhatsApp\Models\WhatsAppAccount;
use App\Interfaces\WhatsApp\WhatsAppServiceInterface;
use Illuminate\Support\Facades\Log;

class WhatsAppHealthCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsapp:health-check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks the health and connection status of WhatsApp accounts.';

    /**
     * Execute the console command.
     */
    public function handle(WhatsAppServiceInterface $whatsAppService)
    {
        $this->info('Starting WhatsApp health check...');

        $accounts = WhatsAppAccount::all();

        if ($accounts->isEmpty()) {
            $this->info('No WhatsApp accounts found.');
            return Command::SUCCESS;
        }

        foreach ($accounts as $account) {
            $status = $whatsAppService->getAccountStatus($account);
            $this->info(sprintf('Account %s (%s) status: %s', $account->display_name ?? $account->phone_number, $account->phone_number, $status));
            Log::info(sprintf('WhatsApp Health Check: Account %s status: %s', $account->phone_number, $status));

            // Optionally, attempt to reconnect if status is 'disconnected' or 'banned'
            if (in_array($status, ['disconnected', 'banned'])) {
                $this->warn(sprintf('Attempting to reconnect account %s...', $account->phone_number));
                // This would typically dispatch a job to handle reconnection asynchronously
                // $whatsAppService->connectAccount($account->user, $account->phone_number, $account->display_name);
            }
        }

        $this->info('WhatsApp health check completed.');

        return Command::SUCCESS;
    }
} 