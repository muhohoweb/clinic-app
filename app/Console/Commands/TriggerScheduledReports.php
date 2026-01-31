<?php

namespace App\Console\Commands;

use App\Models\ScheduledReport;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TriggerScheduledReports extends Command
{
    protected $signature = 'reports:trigger';
    protected $description = 'Trigger scheduled reports execution';

    public function handle()
    {
        Log::info('=== Starting scheduled reports execution ===');
        $this->info('Starting scheduled reports execution...');

        $reports = ScheduledReport::where('is_enabled', true)->get();

        if ($reports->isEmpty()) {
            Log::warning('No active scheduled reports found.');
            $this->warn('No active scheduled reports found.');
            return 0;
        }

        $processed = 0;
        $skipped = 0;

        foreach ($reports as $report) {
            $shouldRun = $this->shouldRunReport($report);

            if ($shouldRun) {
                $report->update(['last_run_at' => Carbon::now()]);

                $message = "✓ Processed: {$report->email} | Frequency: {$report->frequency} | Time: " . Carbon::now()->format('Y-m-d H:i:s');
                Log::info($message);
                $this->info($message);

                $processed++;
            } else {
                $message = "⊘ Skipped: {$report->email} | Frequency: {$report->frequency} | Not due yet";
                Log::info($message);
                $this->comment($message);

                $skipped++;
            }
        }

        $summary = "Summary: {$processed} processed, {$skipped} skipped.";
        Log::info($summary);
        Log::info('=== Finished scheduled reports execution ===');

        $this->info("---");
        $this->info($summary);
        return 0;
    }

    private function shouldRunReport(ScheduledReport $report): bool
    {
        if (!$report->last_run_at) {
            $message = "First run for {$report->email}";
            Log::info("  → {$message}");
            $this->line("  → {$message}");
            return true;
        }

        $now = Carbon::now();
        $lastRun = Carbon::parse($report->last_run_at);

        switch ($report->frequency) {
            case 'daily':
                $shouldRun = $lastRun->diffInDays($now) >= 1;
                $message = "Daily check for {$report->email}: Last run {$lastRun->diffForHumans()}";
                Log::info("  → {$message}");
                $this->line("  → {$message}");
                return $shouldRun;

            case 'weekly':
                $shouldRun = $lastRun->diffInWeeks($now) >= 1;
                $message = "Weekly check for {$report->email}: Last run {$lastRun->diffForHumans()}";
                Log::info("  → {$message}");
                $this->line("  → {$message}");
                return $shouldRun;

            case 'bi-weekly':
                $shouldRun = $lastRun->diffInWeeks($now) >= 2;
                $message = "Bi-weekly check for {$report->email}: Last run {$lastRun->diffForHumans()}";
                Log::info("  → {$message}");
                $this->line("  → {$message}");
                return $shouldRun;

            case 'monthly':
                $shouldRun = $lastRun->diffInMonths($now) >= 1;
                $message = "Monthly check for {$report->email}: Last run {$lastRun->diffForHumans()}";
                Log::info("  → {$message}");
                $this->line("  → {$message}");
                return $shouldRun;

            case 'quarterly':
                $shouldRun = $lastRun->diffInMonths($now) >= 3;
                $message = "Quarterly check for {$report->email}: Last run {$lastRun->diffForHumans()}";
                Log::info("  → {$message}");
                $this->line("  → {$message}");
                return $shouldRun;

            default:
                $message = "Unknown frequency: {$report->frequency} for {$report->email}";
                Log::error("  → {$message}");
                $this->error("  → {$message}");
                return false;
        }
    }
}