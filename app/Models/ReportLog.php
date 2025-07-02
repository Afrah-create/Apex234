<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class ReportLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'scheduled_report_id',
        'report_type',
        'report_config',
        'format',
        'status',
        'file_path',
        'recipients',
        'delivery_status',
        'error_message',
        'generated_at',
        'delivered_at',
        'generated_by'
    ];

    protected $casts = [
        'report_config' => 'array',
        'recipients' => 'array',
        'delivery_status' => 'array',
        'generated_at' => 'datetime',
        'delivered_at' => 'datetime'
    ];

    /**
     * Get the scheduled report this log belongs to
     */
    public function scheduledReport(): BelongsTo
    {
        return $this->belongsTo(ScheduledReport::class);
    }

    /**
     * Get the user who generated this report
     */
    public function generator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    /**
     * Mark report as generating
     */
    public function markAsGenerating(): void
    {
        $this->status = 'generating';
        $this->save();
    }

    /**
     * Mark report as completed
     */
    public function markAsCompleted(string $filePath): void
    {
        $this->status = 'completed';
        $this->file_path = $filePath;
        $this->generated_at = Carbon::now();
        $this->save();
    }

    /**
     * Mark report as failed
     */
    public function markAsFailed(string $errorMessage): void
    {
        $this->status = 'failed';
        $this->error_message = $errorMessage;
        $this->save();
    }

    /**
     * Mark report as delivered
     */
    public function markAsDelivered(): void
    {
        $this->status = 'delivered';
        $this->delivered_at = Carbon::now();
        $this->save();
    }

    /**
     * Update delivery status for a specific recipient
     */
    public function updateDeliveryStatus(string $email, string $status, ?string $errorMessage = null): void
    {
        $deliveryStatus = $this->delivery_status ?? [];
        $deliveryStatus[$email] = [
            'status' => $status,
            'delivered_at' => $status === 'delivered' ? Carbon::now()->toISOString() : null,
            'error_message' => $errorMessage
        ];
        
        $this->delivery_status = $deliveryStatus;
        $this->save();
    }

    /**
     * Get status options
     */
    public static function getStatusOptions(): array
    {
        return [
            'pending' => 'Pending',
            'generating' => 'Generating',
            'completed' => 'Completed',
            'failed' => 'Failed',
            'delivered' => 'Delivered'
        ];
    }

    /**
     * Get delivery status options
     */
    public static function getDeliveryStatusOptions(): array
    {
        return [
            'pending' => 'Pending',
            'sent' => 'Sent',
            'delivered' => 'Delivered',
            'failed' => 'Failed'
        ];
    }
} 