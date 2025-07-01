<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'name',
        'role',
        'vendor_id',
        'status',
        'user_id',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
