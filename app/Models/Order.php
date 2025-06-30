<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    
    protected $guarded = [];
    
    protected $casts = [
        'order_date' => 'datetime',
        'requested_delivery_date' => 'datetime',
        'actual_delivery_date' => 'datetime',
    ];
    
    /**
     * Get the retailer that owns the order
     */
    public function retailer()
    {
        return $this->belongsTo(Retailer::class);
    }
    
    /**
     * Get the distribution center for the order
     */
    public function distributionCenter()
    {
        return $this->belongsTo(DistributionCenter::class);
    }
    
    /**
     * Get the order items for the order
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    
    /**
     * Get the delivery for the order
     */
    public function delivery()
    {
        return $this->hasOne(Delivery::class);
    }
} 