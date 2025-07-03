<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Get the user that owns the supplier.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the drivers for the supplier (max 3 per supplier, enforced in logic).
     */
    public function drivers()
    {
        return $this->hasMany(Driver::class);
    }
}
