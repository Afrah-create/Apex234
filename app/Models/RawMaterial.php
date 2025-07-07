<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'dairy_farm_id',
        'supplier_id',
        'vendor_id',
        'material_name',
        'material_code',
        'material_type',
        'description',
        'quantity',
        'unit_of_measure',
        'unit_price',
        'total_cost',
        'harvest_date',
        'expiry_date',
        'quality_grade',
        'temperature',
        'ph_level',
        'fat_content',
        'protein_content',
        'status',
        'quality_notes',
    ];

    protected $casts = [
        'harvest_date' => 'date',
        'expiry_date' => 'date',
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'temperature' => 'decimal:2',
        'ph_level' => 'decimal:1',
        'fat_content' => 'decimal:2',
        'protein_content' => 'decimal:2',
    ];

    public function dairyFarm()
    {
        return $this->belongsTo(DairyFarm::class);
    }
} 