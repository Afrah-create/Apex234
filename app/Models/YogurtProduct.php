<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YogurtProduct extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function inventories()
    {
        return $this->hasMany(\App\Models\Inventory::class);
    }

    public function getStockAttribute()
    {
        return $this->inventories()->sum('quantity_available');
    }
} 