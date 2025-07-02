<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ReportGenerationService;
use Illuminate\Support\Facades\Log;

class GenerateScheduledReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:generate-scheduled {--force : Force generation of all reports}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate all scheduled reports that are due';

    protected $reportService;

    /**
     * Create a new command instance.
     */
    public function __construct(ReportGenerationService $reportService)
    {
        parent::__construct();
        $this->reportService = $reportService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting scheduled report generation...');
        
        try {
            $results = $this->reportService->processScheduledReports();
            
            $this->info("Report generation completed:");
            $this->info("- Processed: {$results['processed']} reports");
            $this->info("- Successful: {$results['successful']} reports");
            $this->info("- Failed: {$results['failed']} reports");
            
            if (!empty($results['errors'])) {
                $this->warn("Errors encountered:");
                foreach ($results['errors'] as $error) {
                    $this->error("- Report '{$error['report_name']}' (ID: {$error['report_id']}): {$error['error']}");
                }
            }
            
            if ($results['successful'] > 0) {
                $this->info("Successfully generated {$results['successful']} reports.");
            }
            
            if ($results['failed'] > 0) {
                $this->warn("Failed to generate {$results['failed']} reports. Check logs for details.");
            }
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error("Fatal error during report generation: " . $e->getMessage());
            Log::error('Fatal error in scheduled report generation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
} 