<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ScheduledReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $scheduledReport;
    public $reportData;

    /**
     * Create a new message instance.
     */
    public function __construct($scheduledReport, $reportData)
    {
        $this->scheduledReport = $scheduledReport;
        $this->reportData = $reportData;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Scheduled Report: ' . $this->scheduledReport->name)
            ->view('emails.scheduled-report')
            ->with([
                'scheduledReport' => $this->scheduledReport,
                'reportData' => $this->reportData,
            ]);
    }
} 