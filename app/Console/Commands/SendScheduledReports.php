<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ScheduledReport;
use App\Services\ReportGenerationService;
use App\Mail\ScheduledReportMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendScheduledReports extends Command
{
    protected $signature = 'reports:send-scheduled';
    protected $description = 'Send scheduled reports to recipients when their scheduled time is reached';

    public function handle()
    {
        $now = Carbon::now();
        $dueReports = ScheduledReport::where('is_active', true)
            ->where('next_generation_at', '<=', $now)
            ->get();

        if ($dueReports->isEmpty()) {
            $this->info('No scheduled reports to send.');
            return 0;
        }

        foreach ($dueReports as $report) {
            $this->info('Processing scheduled report: ' . $report->name);
            // 1. Generate the report (replace with your actual logic)
            $reportService = app(ReportGenerationService::class);
            $reportData = $reportService->generateReport($report); // Implement this method in your service

            // 2. Email the report to recipients
            foreach ($report->recipients as $email) {
                Mail::to($email)->send(new ScheduledReportMail($report, $reportData));
                $this->info('Sent report to: ' . $email);
            }

            // 3. Update last_generated_at and next_generation_at
            $report->last_generated_at = $now;
            $report->next_generation_at = $this->calculateNextGenerationAt($report, $now);
            $report->save();
        }
        $this->info('All due scheduled reports processed.');
        return 0;
    }

    private function calculateNextGenerationAt($report, $fromTime)
    {
        $from = Carbon::parse($fromTime);
        switch ($report->frequency) {
            case 'daily':
                return $from->copy()->addDay()->setTimeFromTimeString($report->time);
            case 'weekly':
                $dayOfWeek = $report->day_of_week ?? 'Monday';
                return $from->copy()->next($dayOfWeek)->setTimeFromTimeString($report->time);
            case 'monthly':
                $day = $report->day_of_month ?? 1;
                return $from->copy()->addMonth()->day($day)->setTimeFromTimeString($report->time);
            case 'quarterly':
                $day = $report->day_of_month ?? 1;
                return $from->copy()->addMonths(3)->day($day)->setTimeFromTimeString($report->time);
            case 'yearly':
                $day = $report->day_of_month ?? 1;
                return $from->copy()->addYear()->day($day)->setTimeFromTimeString($report->time);
            default:
                return null;
        }
    }
} 