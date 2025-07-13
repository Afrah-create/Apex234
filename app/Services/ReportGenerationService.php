<?php

namespace App\Services;

use App\Models\ScheduledReport;
use App\Models\ReportLog;
use App\Http\Controllers\AdminReportController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class ReportGenerationService
{
    protected $reportController;

    public function __construct(AdminReportController $reportController)
    {
        $this->reportController = $reportController;
    }

    /**
     * Process all scheduled reports that need to be generated
     */
    public function processScheduledReports(): array
    {
        $reports = ScheduledReport::getReportsToGenerate();
        $results = [
            'processed' => 0,
            'successful' => 0,
            'failed' => 0,
            'errors' => []
        ];

        foreach ($reports as $report) {
            try {
                $this->processReport($report);
                $results['successful']++;
            } catch (Exception $e) {
                $results['failed']++;
                $results['errors'][] = [
                    'report_id' => $report->id,
                    'report_name' => $report->name,
                    'error' => $e->getMessage()
                ];
                Log::error('Failed to process scheduled report', [
                    'report_id' => $report->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
            $results['processed']++;
        }

        return $results;
    }

    /**
     * Process a single scheduled report
     */
    public function processReport(ScheduledReport $report): void
    {
        // Create report log entry
        $reportLog = ReportLog::create([
            'scheduled_report_id' => $report->id,
            'report_type' => $report->report_type,
            'report_config' => $report->report_config,
            'format' => $report->format,
            'status' => 'pending',
            'recipients' => $report->recipients,
            'generated_by' => $report->created_by
        ]);

        try {
            // Mark as generating
            $reportLog->markAsGenerating();

            // Generate the report
            $reportData = $this->generateReportData($report);
            
            // Export to file
            $filePath = $this->exportReportToFile($reportData, $report);
            
            // Mark as completed
            $reportLog->markAsCompleted($filePath);
            
            // Send to recipients
            $this->deliverReport($reportLog, $report);
            
            // Update scheduled report
            $report->markAsGenerated();
            
        } catch (Exception $e) {
            $reportLog->markAsFailed($e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate report data using the existing controller
     */
    protected function generateReportData(ScheduledReport $report): array
    {
        $config = $report->report_config;
        $filters = $config['filters'] ?? [];

        // Inject stakeholder-specific filters
        if ($report->stakeholder_type && $report->stakeholder_id) {
            switch ($report->stakeholder_type) {
                case 'vendor':
                    $filters['vendor_id'] = [$report->stakeholder_id];
                    break;
                case 'retailer':
                    $filters['retailer_id'] = [$report->stakeholder_id];
                    break;
                case 'supplier':
                    $filters['supplier_id'] = [$report->stakeholder_id];
                    break;
                case 'employee':
                    $filters['employee_id'] = [$report->stakeholder_id];
                    break;
                // For admin, no filter (gets all data)
            }
        }

        // Create a mock request with the report configuration
        $request = new \Illuminate\Http\Request();
        $request->merge([
            'report_type' => $report->report_type,
            'date_from' => $config['date_from'] ?? Carbon::now()->subDays(30)->format('Y-m-d'),
            'date_to' => $config['date_to'] ?? Carbon::now()->format('Y-m-d'),
            'filters' => $filters,
            'group_by' => $config['group_by'] ?? null,
            'sort_by' => $config['sort_by'] ?? 'created_at',
            'sort_order' => $config['sort_order'] ?? 'desc'
        ]);

        $response = $this->reportController->generateCustomReport($request);
        $responseData = json_decode($response->getContent(), true);

        if (!$responseData['success']) {
            throw new Exception('Failed to generate report data: ' . ($responseData['message'] ?? 'Unknown error'));
        }

        return $responseData['data'];
    }

    /**
     * Export report data to file
     */
    protected function exportReportToFile(array $reportData, ScheduledReport $report): string
    {
        $filename = $report->report_type . '_' . Carbon::now()->format('Y-m-d_H-i-s') . '.' . $report->format;
        $filepath = 'reports/' . $filename;

        switch ($report->format) {
            case 'pdf':
                $content = $this->generatePdfContent($reportData, $report);
                break;
            case 'excel':
                $content = $this->generateExcelContent($reportData, $report);
                break;
            case 'csv':
                $content = $this->generateCsvContent($reportData, $report);
                break;
            default:
                throw new Exception('Unsupported format: ' . $report->format);
        }

        Storage::put($filepath, $content);
        return $filepath;
    }

    /**
     * Generate PDF content
     */
    public function generatePdfContent(array $reportData, ScheduledReport $report, $view = 'admin.reports.user_analysis_pdf'): string
    {
        // Filter only necessary fields for the report
        $summary = $reportData['summary'] ?? [];
        $data = collect($reportData['data'] ?? [])->map(function ($user) {
            return [
                'name' => $user['name'] ?? '',
                'email' => $user['email'] ?? '',
                'role' => $user['role'] ?? '',
                'status' => $user['status'] ?? '',
            ];
        })->toArray();

        // Render a Blade view to HTML
        $html = view($view, [
            'report_name' => $report->name,
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'summary' => $summary,
            'data' => $data,
        ])->render();

        // Generate PDF from HTML
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);

        // Return the raw PDF binary content
        return $pdf->output();
    }

    /**
     * Generate Excel content
     */
    protected function generateExcelContent(array $reportData, ScheduledReport $report): string
    {
        // For now, return CSV content as Excel generation requires additional libraries
        return $this->generateCsvContent($reportData, $report);
    }

    /**
     * Generate CSV content
     */
    protected function generateCsvContent(array $reportData, ScheduledReport $report): string
    {
        $data = $reportData['data'] ?? [];
        
        if (empty($data)) {
            return "No data available\n";
        }

        $output = fopen('php://temp', 'r+');
        
        // Write headers
        fputcsv($output, array_keys($data[0]));
        
        // Write data
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
        
        rewind($output);
        $content = stream_get_contents($output);
        fclose($output);
        
        return $content;
    }

    /**
     * Deliver report to recipients
     */
    protected function deliverReport(ReportLog $reportLog, ScheduledReport $report): void
    {
        foreach ($report->recipients as $email) {
            try {
                $this->sendReportEmail($email, $report, $reportLog);
                $reportLog->updateDeliveryStatus($email, 'delivered');
            } catch (Exception $e) {
                $reportLog->updateDeliveryStatus($email, 'failed', $e->getMessage());
                Log::error('Failed to deliver report to recipient', [
                    'email' => $email,
                    'report_id' => $report->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Mark as delivered if at least one recipient received it
        $deliveryStatus = $reportLog->delivery_status ?? [];
        $deliveredCount = count(array_filter($deliveryStatus, fn($status) => $status['status'] === 'delivered'));
        
        if ($deliveredCount > 0) {
            $reportLog->markAsDelivered();
        }
    }

    /**
     * Send report email to recipient
     */
    protected function sendReportEmail(string $email, ScheduledReport $report, ReportLog $reportLog): void
    {
        // For now, just log the email sending
        // In production, you'd use Laravel's Mail facade with a proper email template
        Log::info('Report email would be sent', [
            'to' => $email,
            'report_name' => $report->name,
            'report_type' => $report->report_type,
            'format' => $report->format,
            'file_path' => $reportLog->file_path
        ]);

        // Example of how you'd send the email:
        /*
        Mail::send('emails.scheduled-report', [
            'report' => $report,
            'reportLog' => $reportLog
        ], function ($message) use ($email, $report, $reportLog) {
            $message->to($email)
                   ->subject('Scheduled Report: ' . $report->name)
                   ->attach(Storage::path($reportLog->file_path));
        });
        */
    }

    /**
     * Generate a report for a scheduled report (stub for scheduled sending)
     */
    public function generateReport($scheduledReport)
    {
        // TODO: Implement actual report generation logic based on $scheduledReport
        // For now, return dummy data or a string
        return 'This is a dummy report for ' . $scheduledReport->name;
    }

    /**
     * Create a new scheduled report
     */
    public function createScheduledReport(array $data): ScheduledReport
    {
        $report = ScheduledReport::create($data);
        $report->updateNextGenerationTime();
        return $report;
    }

    /**
     * Update a scheduled report
     */
    public function updateScheduledReport(ScheduledReport $report, array $data): ScheduledReport
    {
        $report->update($data);
        $report->updateNextGenerationTime();
        return $report;
    }

    /**
     * Delete a scheduled report
     */
    public function deleteScheduledReport(ScheduledReport $report): bool
    {
        // Delete associated report logs
        $report->reportLogs()->delete();
        
        // Delete the scheduled report
        return $report->delete();
    }

    /**
     * Get report statistics
     */
    public function getReportStatistics(): array
    {
        $totalScheduled = ScheduledReport::count();
        $activeScheduled = ScheduledReport::where('is_active', true)->count();
        $totalGenerated = ReportLog::count();
        $recentGenerated = ReportLog::where('created_at', '>=', Carbon::now()->subDays(7))->count();
        $failedReports = ReportLog::where('status', 'failed')->count();

        return [
            'total_scheduled' => $totalScheduled,
            'active_scheduled' => $activeScheduled,
            'total_generated' => $totalGenerated,
            'recent_generated' => $recentGenerated,
            'failed_reports' => $failedReports,
            'success_rate' => $totalGenerated > 0 ? (($totalGenerated - $failedReports) / $totalGenerated) * 100 : 0
        ];
    }
} 