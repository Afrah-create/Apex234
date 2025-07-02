<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorApplicant extends Model
{
    use HasFactory;

    protected $table = 'vendor_applicant';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'company_name',
        'pdf_path',
        'status',
        'visit_date',
        'validation_message',
    ];
} 