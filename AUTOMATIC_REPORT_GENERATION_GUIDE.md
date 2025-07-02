# Automatic Report Generation System

This document provides a comprehensive guide to the automatic report generation functionality implemented in the Caramel YG dashboard.

## Overview

The automatic report generation system allows administrators to schedule reports to be generated and delivered automatically at specified intervals. The system supports multiple report types, formats, and delivery methods.

## Features

### Core Functionality
- **Scheduled Reports**: Create reports that run automatically at specified intervals
- **Multiple Frequencies**: Daily, weekly, monthly, quarterly, and yearly scheduling
- **Multiple Formats**: PDF, Excel, and CSV export formats
- **Email Delivery**: Automatic delivery to multiple recipients
- **Report Logging**: Complete audit trail of report generation and delivery
- **Manual Triggering**: Ability to run scheduled reports on-demand
- **Status Management**: Enable/disable scheduled reports

### Report Types Supported
1. **Sales Summary Report**: Comprehensive sales analysis with trends and comparisons
2. **Inventory Status Report**: Current inventory levels, stock movements, and alerts
3. **Supplier Performance Report**: Supplier metrics, delivery times, and quality ratings
4. **Financial Summary Report**: Revenue, costs, profit margins, and financial trends
5. **Customer Analysis Report**: Customer behavior, preferences, and satisfaction metrics
6. **Production Metrics Report**: Production efficiency, capacity utilization, and quality metrics

## Database Structure

### Tables

#### `scheduled_reports`
- `id`: Primary key
- `name`: Report name
- `description`: Report description
- `report_type`: Type of report to generate
- `report_config`: JSON configuration for the report
- `frequency`: Scheduling frequency (daily, weekly, monthly, quarterly, yearly)
- `day_of_week`: Day of week for weekly reports
- `day_of_month`: Day of month for monthly/quarterly/yearly reports
- `time`: Time to generate report
- `timezone`: Timezone for scheduling
- `recipients`: JSON array of email addresses
- `format`: Export format (pdf, excel, csv)
- `is_active`: Whether the report is active
- `last_generated_at`: Timestamp of last generation
- `next_generation_at`: Timestamp of next scheduled generation
- `created_by`: User who created the report
- `created_at`, `updated_at`: Timestamps

#### `report_logs`
- `id`: Primary key
- `scheduled_report_id`: Foreign key to scheduled_reports
- `report_type`: Type of report generated
- `report_config`: JSON configuration used
- `format`: Export format used
- `status`: Generation status (pending, generating, completed, failed, delivered)
- `file_path`: Path to generated file
- `recipients`: JSON array of recipients
- `delivery_status`: JSON tracking delivery to each recipient
- `error_message`: Error message if generation failed
- `generated_at`: Timestamp when report was generated
- `delivered_at`: Timestamp when report was delivered
- `generated_by`: User who triggered the generation
- `created_at`, `updated_at`: Timestamps

## API Endpoints

### Scheduled Reports Management

#### Get All Scheduled Reports
```
GET /api/reports/scheduled
```

#### Create Scheduled Report
```
POST /api/reports/scheduled
```

**Request Body:**
```json
{
    "name": "Daily Sales Summary",
    "description": "Daily sales summary report",
    "report_type": "sales_summary",
    "report_config": {
        "date_from": "2024-01-01",
        "date_to": "2024-01-31",
        "filters": {},
        "group_by": "daily",
        "sort_by": "created_at",
        "sort_order": "desc"
    },
    "frequency": "daily",
    "day_of_week": null,
    "day_of_month": null,
    "time": "09:00",
    "timezone": "UTC",
    "recipients": ["admin@example.com", "manager@example.com"],
    "format": "pdf"
}
```

#### Update Scheduled Report
```
PUT /api/reports/scheduled/{id}
```

#### Delete Scheduled Report
```
DELETE /api/reports/scheduled/{id}
```

#### Toggle Report Status
```
PATCH /api/reports/scheduled/{id}/toggle
```

#### Manually Trigger Report
```
POST /api/reports/scheduled/{id}/trigger
```

### Report Logs

#### Get Report Logs
```
GET /api/reports/logs?status=completed&date_from=2024-01-01&date_to=2024-01-31
```

#### Get Report Statistics
```
GET /api/reports/statistics
```

## Usage

### 1. Creating a Scheduled Report

#### Via Web Interface
1. Navigate to Admin > Reports
2. Configure your report parameters
3. Click "Schedule Report"
4. Fill in the scheduling form:
   - Report name and description
   - Frequency (daily, weekly, monthly, quarterly, yearly)
   - Time and timezone
   - Recipients (comma-separated emails)
   - Export format
5. Click "Schedule Report"

#### Via API
```javascript
const response = await fetch('/api/reports/scheduled', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken
    },
    body: JSON.stringify({
        name: 'Daily Sales Summary',
        description: 'Daily sales summary report',
        report_type: 'sales_summary',
        report_config: {
            date_from: '2024-01-01',
            date_to: '2024-01-31',
            filters: {},
            group_by: 'daily',
            sort_by: 'created_at',
            sort_order: 'desc'
        },
        frequency: 'daily',
        time: '09:00',
        timezone: 'UTC',
        recipients: ['admin@example.com'],
        format: 'pdf'
    })
});
```

### 2. Managing Scheduled Reports

#### View All Scheduled Reports
```javascript
const response = await fetch('/api/reports/scheduled');
const reports = await response.json();
```

#### Enable/Disable a Report
```javascript
const response = await fetch(`/api/reports/scheduled/${reportId}/toggle`, {
    method: 'PATCH',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken
    }
});
```

#### Manually Trigger a Report
```javascript
const response = await fetch(`/api/reports/scheduled/${reportId}/trigger`, {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken
    }
});
```

### 3. Monitoring Report Generation

#### View Report Logs
```javascript
const response = await fetch('/api/reports/logs?status=completed');
const logs = await response.json();
```

#### Get Statistics
```javascript
const response = await fetch('/api/reports/statistics');
const stats = await response.json();
```

## Automation Setup

### 1. Cron Job Configuration

To enable automatic report generation, add the following cron job:

```bash
# Add to your crontab (crontab -e)
* * * * * cd /path/to/your/laravel/app && php artisan reports:generate-scheduled >> /dev/null 2>&1
```

### 2. Command Usage

#### Generate Scheduled Reports
```bash
php artisan reports:generate-scheduled
```

#### Force Generate All Reports
```bash
php artisan reports:generate-scheduled --force
```

### 3. Service Configuration

The system uses the `ReportGenerationService` class to handle report generation. Key methods:

- `processScheduledReports()`: Process all due reports
- `processReport($report)`: Process a single report
- `createScheduledReport($data)`: Create a new scheduled report
- `updateScheduledReport($report, $data)`: Update an existing report
- `deleteScheduledReport($report)`: Delete a scheduled report

## File Storage

Generated reports are stored in the `storage/app/reports/` directory with the following naming convention:
```
{report_type}_{timestamp}.{format}
```

Example: `sales_summary_2024-01-15_09-00-00.pdf`

## Email Delivery

Reports are automatically delivered to specified recipients via email. The system:

1. Generates the report file
2. Creates email with report attached
3. Sends to all specified recipients
4. Tracks delivery status for each recipient
5. Updates the report log with delivery information

## Error Handling

The system includes comprehensive error handling:

- **Generation Errors**: Logged with detailed error messages
- **Delivery Errors**: Tracked per recipient
- **Retry Logic**: Failed reports can be manually retriggered
- **Status Tracking**: Complete audit trail of all operations

## Security Considerations

- All endpoints require authentication and admin middleware
- File downloads are secured with proper authorization
- Email addresses are validated before scheduling
- Report configurations are validated to prevent injection attacks

## Monitoring and Maintenance

### Log Files
Check Laravel logs for detailed information:
```bash
tail -f storage/logs/laravel.log
```

### Database Monitoring
Monitor the `report_logs` table for generation status:
```sql
SELECT status, COUNT(*) FROM report_logs GROUP BY status;
```

### Storage Monitoring
Monitor report file storage:
```bash
du -sh storage/app/reports/
```

## Troubleshooting

### Common Issues

1. **Reports Not Generating**
   - Check if cron job is running
   - Verify scheduled reports are active
   - Check Laravel logs for errors

2. **Email Delivery Issues**
   - Verify email configuration in `.env`
   - Check recipient email addresses
   - Review delivery status in report logs

3. **File Generation Errors**
   - Check storage permissions
   - Verify disk space
   - Review report configuration

### Debug Commands

```bash
# Check scheduled reports
php artisan tinker
>>> App\Models\ScheduledReport::where('is_active', true)->get()

# Check report logs
php artisan tinker
>>> App\Models\ReportLog::latest()->take(10)->get()

# Manually trigger a report
php artisan tinker
>>> $service = app(App\Services\ReportGenerationService::class);
>>> $report = App\Models\ScheduledReport::find(1);
>>> $service->processReport($report);
```

## Future Enhancements

1. **Advanced Scheduling**: More complex scheduling patterns
2. **Report Templates**: Pre-configured report templates
3. **Webhook Integration**: Send reports to external systems
4. **Report Archiving**: Automatic cleanup of old reports
5. **Performance Optimization**: Background job processing
6. **Real-time Notifications**: WebSocket notifications for report status

## Support

For issues or questions regarding the automatic report generation system:

1. Check the Laravel logs for error details
2. Review the report logs in the database
3. Verify cron job configuration
4. Test manual report generation
5. Contact the development team with specific error messages 