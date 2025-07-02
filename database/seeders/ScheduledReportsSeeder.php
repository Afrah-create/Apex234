<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ScheduledReport;
use App\Models\User;
use Carbon\Carbon;

class ScheduledReportsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first admin user
        $adminUser = User::whereHas('roles', function($query) {
            $query->where('name', 'admin');
        })->first();

        if (!$adminUser) {
            $this->command->warn('No admin user found. Skipping scheduled reports seeding.');
            return;
        }

        $scheduledReports = [
            [
                'name' => 'Daily Sales Summary',
                'description' => 'Daily sales summary report for management review',
                'report_type' => 'sales_summary',
                'report_config' => [
                    'date_from' => Carbon::now()->subDays(1)->format('Y-m-d'),
                    'date_to' => Carbon::now()->format('Y-m-d'),
                    'filters' => [],
                    'group_by' => 'daily',
                    'sort_by' => 'created_at',
                    'sort_order' => 'desc'
                ],
                'frequency' => 'daily',
                'day_of_week' => null,
                'day_of_month' => null,
                'time' => '08:00:00',
                'timezone' => 'UTC',
                'recipients' => ['admin@example.com', 'manager@example.com'],
                'format' => 'pdf',
                'is_active' => true,
                'created_by' => $adminUser->id
            ],
            [
                'name' => 'Weekly Inventory Status',
                'description' => 'Weekly inventory status report for supply chain management',
                'report_type' => 'inventory_status',
                'report_config' => [
                    'date_from' => Carbon::now()->subDays(7)->format('Y-m-d'),
                    'date_to' => Carbon::now()->format('Y-m-d'),
                    'filters' => [],
                    'group_by' => 'product',
                    'sort_by' => 'total_available',
                    'sort_order' => 'desc'
                ],
                'frequency' => 'weekly',
                'day_of_week' => 'Monday',
                'day_of_month' => null,
                'time' => '09:00:00',
                'timezone' => 'UTC',
                'recipients' => ['inventory@example.com', 'supplychain@example.com'],
                'format' => 'excel',
                'is_active' => true,
                'created_by' => $adminUser->id
            ],
            [
                'name' => 'Monthly Financial Summary',
                'description' => 'Monthly financial summary for executive review',
                'report_type' => 'financial_summary',
                'report_config' => [
                    'date_from' => Carbon::now()->startOfMonth()->format('Y-m-d'),
                    'date_to' => Carbon::now()->endOfMonth()->format('Y-m-d'),
                    'filters' => [],
                    'group_by' => 'monthly',
                    'sort_by' => 'total_revenue',
                    'sort_order' => 'desc'
                ],
                'frequency' => 'monthly',
                'day_of_week' => null,
                'day_of_month' => 1,
                'time' => '10:00:00',
                'timezone' => 'UTC',
                'recipients' => ['finance@example.com', 'ceo@example.com'],
                'format' => 'pdf',
                'is_active' => true,
                'created_by' => $adminUser->id
            ],
            [
                'name' => 'Quarterly Supplier Performance',
                'description' => 'Quarterly supplier performance analysis',
                'report_type' => 'supplier_performance',
                'report_config' => [
                    'date_from' => Carbon::now()->startOfQuarter()->format('Y-m-d'),
                    'date_to' => Carbon::now()->endOfQuarter()->format('Y-m-d'),
                    'filters' => [],
                    'group_by' => null,
                    'sort_by' => 'created_at',
                    'sort_order' => 'desc'
                ],
                'frequency' => 'quarterly',
                'day_of_week' => null,
                'day_of_month' => 1,
                'time' => '11:00:00',
                'timezone' => 'UTC',
                'recipients' => ['procurement@example.com', 'quality@example.com'],
                'format' => 'excel',
                'is_active' => true,
                'created_by' => $adminUser->id
            ],
            [
                'name' => 'Yearly User Analysis',
                'description' => 'Annual user analysis and performance metrics for retailers, vendors, and suppliers',
                'report_type' => 'user_analysis',
                'report_config' => [
                    'date_from' => Carbon::now()->startOfYear()->format('Y-m-d'),
                    'date_to' => Carbon::now()->endOfYear()->format('Y-m-d'),
                    'filters' => [],
                    'group_by' => 'role',
                    'sort_by' => 'created_at',
                    'sort_order' => 'desc'
                ],
                'frequency' => 'yearly',
                'day_of_week' => null,
                'day_of_month' => 1,
                'time' => '12:00:00',
                'timezone' => 'UTC',
                'recipients' => ['admin@example.com', 'management@example.com'],
                'format' => 'pdf',
                'is_active' => true,
                'created_by' => $adminUser->id
            ]
        ];

        foreach ($scheduledReports as $reportData) {
            $report = ScheduledReport::create($reportData);
            $report->updateNextGenerationTime();
        }

        $this->command->info('Scheduled reports seeded successfully!');
    }
} 