<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'name',
        'role',
        'vendor_id',
        'user_id',
        'status',
        'distribution_center_id',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function distributionCenter()
    {
        return $this->belongsTo(\App\Models\DistributionCenter::class);
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isInactive()
    {
        return $this->status === 'inactive';
    }

    public function isTerminated()
    {
        return $this->status === 'terminated';
    }
}
