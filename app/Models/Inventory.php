<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;
    protected $guarded = [];
    
    public function yogurtProduct()
    {
        return $this->belongsTo(YogurtProduct::class);
    }

    public function distributionCenter()
    {
        return $this->belongsTo(DistributionCenter::class, 'distribution_center_id');
    }
} 