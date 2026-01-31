<?php

namespace App\Console\Commands;

use App\Models\ScheduledReport;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

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

    public function handle(): int
    {
        $this->info('Starting scheduled reports execution...');

        $reports = ScheduledReport::where('is_enabled', true)->get();

        if ($reports->isEmpty()) {
            $this->warn('No active scheduled reports found.');
            return 0;
        }

        $processed = 0;
        $skipped = 0;

        foreach ($reports as $report) {
            $shouldRun = $this->shouldRunReport($report);

            if ($shouldRun) {
                $report->update(['last_run_at' => Carbon::now()]);
                $this->info("✓ Processed: {$report->email} | Frequency: {$report->frequency} | Time: " . Carbon::now()->format('Y-m-d H:i:s'));
                $processed++;
            } else {
                $this->comment("⊘ Skipped: {$report->email} | Frequency: {$report->frequency} | Not due yet");
                $skipped++;
            }
        }

        $this->info("---");
        $this->info("Summary: {$processed} processed, {$skipped} skipped.");
        return 0;
    }

    /**
     * Check if report should run based on frequency
     */
    private function shouldRunReport(ScheduledReport $report): bool
    {
        // If never run before, run it
        if (!$report->last_run_at) {
            $this->line("  → First run for {$report->email}");
            return true;
        }

        $now = Carbon::now();
        $lastRun = Carbon::parse($report->last_run_at);

        switch ($report->frequency) {
            case 'daily':
                $shouldRun = $lastRun->diffInDays($now) >= 1;
                $this->line("  → Daily check: Last run {$lastRun->diffForHumans()}");
                return $shouldRun;

            case 'weekly':
                $shouldRun = $lastRun->diffInWeeks($now) >= 1;
                $this->line("  → Weekly check: Last run {$lastRun->diffForHumans()}");
                return $shouldRun;

            case 'bi-weekly':
                $shouldRun = $lastRun->diffInWeeks($now) >= 2;
                $this->line("  → Bi-weekly check: Last run {$lastRun->diffForHumans()}");
                return $shouldRun;

            case 'monthly':
                $shouldRun = $lastRun->diffInMonths($now) >= 1;
                $this->line("  → Monthly check: Last run {$lastRun->diffForHumans()}");
                return $shouldRun;

            case 'quarterly':
                $shouldRun = $lastRun->diffInMonths($now) >= 3;
                $this->line("  → Quarterly check: Last run {$lastRun->diffForHumans()}");
                return $shouldRun;

            default:
                $this->error("  → Unknown frequency: {$report->frequency}");
                return false;
        }
    }
}