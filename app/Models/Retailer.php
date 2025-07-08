<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Retailer extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'store_name',
        'store_code',
        'store_address',
        'store_phone',
        'store_email',
        'store_manager',
        'manager_phone',
        'manager_email',
        'store_type',
        'store_size',
        'daily_customer_traffic',
        'monthly_sales_volume',
        'payment_methods',
        'store_hours',
        'certification_status',
        'certifications',
        'last_inspection_date',
        'next_inspection_date',
        'customer_rating',
        'status',
        'notes',
        'business_name',
        'business_address',
        'contact_person',
        'contact_email',
        'contact_phone',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 