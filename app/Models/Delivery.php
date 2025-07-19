<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function driver()
    {
        return $this->belongsTo(\App\Models\Employee::class, 'driver_id');
    }
} 