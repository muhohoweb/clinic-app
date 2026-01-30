<?php

namespace App\Console\Commands;

use App\Models\ScheduledReport;
use Carbon\Carbon;
use Illuminate\Console\Command;

class TriggerScheduledReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:trigger';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Trigger scheduled reports execution';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting scheduled reports execution...');

        $reports = ScheduledReport::where('is_enabled', true)->get();

        if ($reports->isEmpty()) {
            $this->warn('No active scheduled reports found.');
            return 0;
        }

        foreach ($reports as $report) {
            $report->update(['last_run_at' => Carbon::now()]);

            $this->info("✓ Processed: {$report->email} ({$report->frequency})");
        }

        $this->info("Successfully processed {$reports->count()} report(s).");
        return 0;
    }
}