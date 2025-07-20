<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawMaterialOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'supplier_id',
        'material_type',
        'material_name',
        'quantity',
        'unit_of_measure',
        'unit_price',
        'total_amount',
        'status',
        'notes',
        'order_date',
        'expected_delivery_date',
        'actual_delivery_date',
        'archived',
    ];

    protected $casts = [
        'order_date' => 'datetime',
        'expected_delivery_date' => 'datetime',
        'actual_delivery_date' => 'datetime',
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'archived' => 'boolean',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function getStatusBadgeAttribute()
    {
        $colors = [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'confirmed' => 'bg-blue-100 text-blue-800',
            'processing' => 'bg-purple-100 text-purple-800',
            'shipped' => 'bg-indigo-100 text-indigo-800',
            'delivered' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            'unavailable' => 'bg-gray-100 text-gray-800',
        ];

        $color = $colors[$this->status] ?? 'bg-gray-100 text-gray-800';
        return "<span class=\"px-3 py-1 rounded-full text-xs font-semibold {$color}\">" . 
               ucfirst($this->status) . "</span>";
    }
} 