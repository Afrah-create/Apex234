<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = ['user_id', 'cart_data'];
    protected $casts = [
        'cart_data' => 'array',
    ];
} 