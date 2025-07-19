<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'name',
        'phone',
        'email',
        'address',
        'date_of_birth',
        'license',
        'license_expiry',
        'photo',
        'emergency_contact',
        'vehicle_number',
    ];

    /**
     * Get the supplier that owns the driver.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the deliveries assigned to the driver.
     */
    public function deliveries()
    {
        return $this->hasMany(\App\Models\Delivery::class, 'driver_id');
    }
} 