<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistributionCenter extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function vendors()
    {
        return $this->belongsToMany(\App\Models\Vendor::class, 'distribution_center_vendor');
    }
} 