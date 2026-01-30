<?php

namespace App\Http\Controllers;

use App\Models\ScheduledReport;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ScheduledReportController extends Controller
{
    /**
     * Store or update scheduled report settings
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'frequency' => 'required|in:daily,weekly,bi-weekly,monthly,quarterly',
            'scheduled_time' => 'nullable|date_format:H:i',
        ]);

        $validated['scheduled_time'] = $validated['scheduled_time'] ?? '08:00';
        $validated['is_enabled'] = true;

        $schedule = ScheduledReport::query()->updateOrCreate(
            ['email' => $validated['email']],
            $validated
        );

        return response()->json([
            'success' => true,
            'message' => 'Schedule saved successfully',
            'data' => $schedule
        ]);
    }

    /**
     * API endpoint to trigger cron job
     */
    public function trigger()
    {
        $reports = ScheduledReport::query()->where('is_enabled', true)->get();

        $processed = [];
        foreach ($reports as $report) {
            // Mark as run
            $report->update(['last_run_at' => Carbon::now()]);

            $processed[] = [
                'email' => $report->email,
                'frequency' => $report->frequency,
                'executed_at' => Carbon::now()
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Cron executed successfully',
            'processed' => $processed
        ]);
    }
}