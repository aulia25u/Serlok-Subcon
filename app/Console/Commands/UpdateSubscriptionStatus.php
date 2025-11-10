<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use Carbon\Carbon;

class UpdateSubscriptionStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update subscription status to penagihan if valid_until has passed by 7 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating subscription statuses...');

        // Update status to 'penagihan'
        $toPenagihan = Subscription::where('status', 'active')
            ->where('valid_until', '<', Carbon::now())
            ->where('valid_until', '>=', Carbon::now()->subDays(7))
            ->get();

        foreach ($toPenagihan as $subscription) {
            $subscription->update(['status' => 'penagihan']);
            $this->info("Subscription #{$subscription->id} status updated to penagihan.");
        }

        // Update status to 'non-active'
        $toNonActive = Subscription::whereIn('status', ['active', 'penagihan'])
            ->where('valid_until', '<', Carbon::now()->subDays(7))
            ->get();

        foreach ($toNonActive as $subscription) {
            $subscription->update(['status' => 'non-active']);
            $this->info("Subscription #{$subscription->id} status updated to non-active.");
        }

        $this->info('Done.');
    }
}