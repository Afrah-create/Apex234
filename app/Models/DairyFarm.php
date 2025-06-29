<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DairyFarm extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'farm_name',
        'farm_code',
        'farm_address',
        'farm_phone',
        'farm_email',
        'farm_manager',
        'manager_phone',
        'manager_email',
        'total_cattle',
        'milking_cattle',
        'daily_milk_production',
        'certification_status',
        'certifications',
        'last_inspection_date',
        'next_inspection_date',
        'quality_rating',
        'status',
        'notes',
    ];

    protected $casts = [
        'certifications' => 'array',
        'last_inspection_date' => 'date',
        'next_inspection_date' => 'date',
        'daily_milk_production' => 'decimal:2',
        'quality_rating' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function rawMaterials()
    {
        return $this->hasMany(RawMaterial::class);
    }
} 