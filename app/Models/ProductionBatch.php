<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionBatch extends Model
{
    use HasFactory;
    protected $fillable = [
        'vendor_id',
        'product_id',
        'quantity_produced',
        'batch_code',
    ];
    public function vendor() {
        return $this->belongsTo(Vendor::class);
    }
    public function product() {
        return $this->belongsTo(YogurtProduct::class, 'product_id');
    }
    public function rawMaterials() {
        return $this->belongsToMany(RawMaterial::class, 'production_batch_raw_materials')
            ->withPivot('quantity_used')
            ->withTimestamps();
    }
} 