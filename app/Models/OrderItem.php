<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;
    
    protected $guarded = [];
    
    protected $casts = [
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];
    
    /**
     * Get the order that owns the order item
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    
    /**
     * Get the yogurt product for the order item
     */
    public function yogurtProduct()
    {
        return $this->belongsTo(YogurtProduct::class);
    }
} 