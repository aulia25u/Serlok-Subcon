<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PosQueue;
use Carbon\Carbon;

class ProcessScheduledPosSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pos:sync-scheduled {--dry-run : Run without making changes} {--run-force : Force run regardless of schedule time}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process scheduled POS data sync for customers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting scheduled POS sync process...');

        $isDryRun = $this->option('dry-run');
        $isForceRun = $this->option('run-force');

        if ($isDryRun) {
            $this->warn('Running in DRY RUN mode - no changes will be made');
        }

        if ($isForceRun) {
            $this->warn('Running in FORCE mode - will process all schedules regardless of time');
        }

        // Get all scheduled POS queues
        $scheduledQueues = PosQueue::where('is_scheduled', true)->get();

        if ($scheduledQueues->isEmpty()) {
            $this->info('No scheduled POS syncs found.');
            return;
        }

        $this->info("Found {$scheduledQueues->count()} scheduled sync(s)");

        $now = Carbon::now('Asia/Jakarta');
        $today = $now->toDateString();
        $currentTime = $now->format('H:i');

        foreach ($scheduledQueues as $queue) {
            $customerName = $queue->customer ? $queue->customer->name : 'Unknown';
            $this->line("Processing customer: {$customerName}");

            // Check if it's time to run (schedule_time matches current time) unless force run
            $scheduleTimeFormatted = Carbon::createFromFormat('H:i:s', $queue->schedule_time)->format('H:i');
            if (!$isForceRun && $scheduleTimeFormatted !== $currentTime) {
                $this->line("  - Schedule time {$queue->schedule_time} doesn't match current time {$currentTime}, skipping");
                continue;
            }

            // Check if already ran today (last_run stored in Asia/Jakarta timezone)
            $lastRunDate = $queue->last_run ? Carbon::parse($queue->last_run)->toDateString() : null;
            $today = Carbon::now('Asia/Jakarta')->toDateString();

            if ($lastRunDate === $today) {
                $this->line("  - Already processed today, skipping");
                continue;
            }

            $this->line("  - Creating POS sync record for today's data");

            // Create timestamps for today's date using Carbon
            $startOfDay = $now->copy()->startOfDay()->addSecond(); // 00:00:01
            $endOfDay = $now->copy()->endOfDay()->subSecond(); // 23:59:59

            // Create new pos_queue record for today's data
            $newQueueData = [
                'customer_id' => $queue->customer_id,
                'start_date' => $startOfDay->timestamp,
                'end_date' => $endOfDay->timestamp,
                'telegram_chat_id' => $queue->telegram_chat_id,
                'status' => 'pending',
                'is_scheduled' => false, // This is the actual sync record
                'schedule_time' => null,
                'last_run' => null,
            ];

            if (!$isDryRun) {
                PosQueue::create($newQueueData);

                // Update last_run on the scheduled record
                $queue->update(['last_run' => $now]);
            }

            $this->line("  - POS sync record created for date: {$now->format('d-m-Y')}");
        }

        $this->info('Scheduled POS sync process completed.');
    }
}
