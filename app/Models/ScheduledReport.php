<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class ScheduledReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'report_type',
        'report_config',
        'frequency',
        'day_of_week',
        'day_of_month',
        'time',
        'timezone',
        'recipients',
        'format',
        'is_active',
        'last_generated_at',
        'next_generation_at',
        'created_by',
        // Stakeholder fields
        'stakeholder_type', // e.g., admin, vendor, retailer, supplier, employee
        'stakeholder_id',   // ID of the stakeholder (nullable)
    ];

    protected $casts = [
        'report_config' => 'array',
        'recipients' => 'array',
        'is_active' => 'boolean',
        'last_generated_at' => 'datetime',
        'next_generation_at' => 'datetime',
        'time' => 'datetime:H:i:s'
    ];

    /**
     * Get the user who created this scheduled report
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the report logs for this scheduled report
     */
    public function reportLogs(): HasMany
    {
        return $this->hasMany(ReportLog::class);
    }

    /**
     * Calculate the next generation time based on frequency
     */
    public function calculateNextGenerationTime(): Carbon
    {
        $now = Carbon::now()->setTimezone($this->timezone);
        $time = Carbon::parse($this->time)->setTimezone($this->timezone);
        
        switch ($this->frequency) {
            case 'daily':
                $next = $now->copy()->setTime($time->hour, $time->minute, $time->second);
                if ($next->lte($now)) {
                    $next->addDay();
                }
                break;
                
            case 'weekly':
                $next = $now->copy()->next($this->day_of_week)->setTime($time->hour, $time->minute, $time->second);
                break;
                
            case 'monthly':
                $next = $now->copy()->setDay($this->day_of_month)->setTime($time->hour, $time->minute, $time->second);
                if ($next->lte($now)) {
                    $next->addMonth();
                }
                break;
                
            case 'quarterly':
                $next = $now->copy()->setDay($this->day_of_month)->setTime($time->hour, $time->minute, $time->second);
                while ($next->lte($now) || $next->quarter !== $now->quarter) {
                    $next->addMonth(3);
                }
                break;
                
            case 'yearly':
                $next = $now->copy()->setDay($this->day_of_month)->setTime($time->hour, $time->minute, $time->second);
                if ($next->lte($now)) {
                    $next->addYear();
                }
                break;
                
            default:
                $next = $now->copy()->addDay();
        }
        
        return $next;
    }

    /**
     * Check if the report should be generated now
     */
    public function shouldGenerateNow(): bool
    {
        if (!$this->is_active) {
            return false;
        }
        
        return $this->next_generation_at && $this->next_generation_at->lte(Carbon::now());
    }

    /**
     * Update the next generation time
     */
    public function updateNextGenerationTime(): void
    {
        $this->next_generation_at = $this->calculateNextGenerationTime();
        $this->save();
    }

    /**
     * Mark report as generated
     */
    public function markAsGenerated(): void
    {
        $this->last_generated_at = Carbon::now();
        $this->updateNextGenerationTime();
    }

    /**
     * Get reports that need to be generated
     */
    public static function getReportsToGenerate(): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('is_active', true)
            ->where('next_generation_at', '<=', Carbon::now())
            ->get();
    }

    /**
     * Get frequency options
     */
    public static function getFrequencyOptions(): array
    {
        return [
            'daily' => 'Daily',
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
            'quarterly' => 'Quarterly',
            'yearly' => 'Yearly'
        ];
    }

    /**
     * Get day of week options
     */
    public static function getDayOfWeekOptions(): array
    {
        return [
            'Monday' => 'Monday',
            'Tuesday' => 'Tuesday',
            'Wednesday' => 'Wednesday',
            'Thursday' => 'Thursday',
            'Friday' => 'Friday',
            'Saturday' => 'Saturday',
            'Sunday' => 'Sunday'
        ];
    }

    /**
     * Get format options
     */
    public static function getFormatOptions(): array
    {
        return [
            'pdf' => 'PDF',
            'excel' => 'Excel',
            'csv' => 'CSV'
        ];
    }
} 