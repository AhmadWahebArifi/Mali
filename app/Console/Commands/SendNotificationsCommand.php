<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotificationService;

class SendNotificationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:send {type? : Type of notifications to send (monthly|low-balance|all)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications based on user preferences';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $notificationService = new NotificationService();
        $type = $this->argument('type') ?? 'all';

        $this->info('Sending notifications...');

        switch ($type) {
            case 'monthly':
                $reportsSent = $notificationService->sendMonthlyReports();
                $this->info("✅ Monthly reports sent to {$reportsSent} users");
                break;
            
            case 'low-balance':
                $alertsCreated = $notificationService->checkLowBalanceAlerts();
                $this->info("✅ Low balance alerts created for {$alertsCreated} accounts");
                break;
            
            case 'all':
            default:
                $reportsSent = $notificationService->sendMonthlyReports();
                $alertsCreated = $notificationService->checkLowBalanceAlerts();
                $this->info("✅ Monthly reports sent to {$reportsSent} users");
                $this->info("✅ Low balance alerts created for {$alertsCreated} accounts");
                break;
        }

        $this->info('Notification sending completed!');
        return 0;
    }
}
