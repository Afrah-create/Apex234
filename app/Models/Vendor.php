<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'business_name',
        'business_address',
        'phone_number',
        'tax_id',
        'business_license',
        'status',
        'description',
        'contact_person',
        'contact_email',
        'contact_phone',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function yogurtProducts()
    {
        return $this->hasMany(YogurtProduct::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function inventory()
    {
        return $this->hasMany(Inventory::class);
    }

    public function distributionCenters()
    {
        return $this->belongsToMany(\App\Models\DistributionCenter::class, 'distribution_center_vendor');
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function isSuspended()
    {
        return $this->status === 'suspended';
    }
} 