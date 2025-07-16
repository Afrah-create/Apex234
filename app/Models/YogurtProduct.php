<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YogurtProduct extends Model
{
    use HasFactory;
    protected $guarded = [];

    /**
     * Get all inventory records for this yogurt product.
     */
    public function inventory()
    {
        return $this->hasMany(Inventory::class, 'yogurt_product_id');
    }

    /**
     * Get the most recent inventory record for this yogurt product (optional helper).
     */
    public function currentInventory()
    {
        return $this->hasOne(Inventory::class, 'yogurt_product_id')->latestOfMany();
    }

    public function inventories()
    {
        return $this->hasMany(\App\Models\Inventory::class);
    }

    public function getStockAttribute()
    {
        return $this->inventories()->sum('quantity_available');
    }

<<<<<<< HEAD
    public function vendor()
    {
        return $this->belongsTo(\App\Models\Vendor::class);
=======
    public function rawMaterialRequirements()
    {
        // Example: adjust these values per product as needed
        // [material_type => quantity_per_batch]
        return [
            'milk' => 5,    // 5L per batch
            'sugar' => 1,   // 1kg per batch
            'fruit' => 2,   // 2kg per batch
        ];
>>>>>>> b3d5b65b79d7cdae13e09e94d0ae82735a492ac2
    }
} 