<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;

class ScheduledReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $scheduledReport;
    public $reportLog;

    /**
     * Create a new message instance.
     */
    public function __construct($scheduledReport, $reportLog)
    {
        $this->scheduledReport = $scheduledReport;
        $this->reportLog = $reportLog;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $email = $this->subject('Scheduled Report: ' . $this->scheduledReport->name)
            ->view('emails.scheduled-report')
            ->with([
                'scheduledReport' => $this->scheduledReport,
                'reportData' => $this->reportLog->report_config,
            ]);
        if ($this->reportLog->file_path && Storage::disk('public')->exists($this->reportLog->file_path)) {
            $email->attach(
                storage_path('app/public/' . $this->reportLog->file_path)
            );
        }
        return $email;
    }
} 