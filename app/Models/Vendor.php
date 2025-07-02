<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;
    protected $guarded = [];

<<<<<<< HEAD
    public function user()
    {
        return $this->belongsTo(User::class);
=======
    public function employees()
    {
        return $this->hasMany(Employee::class);
>>>>>>> b086cb0c900ffaa41409c246f7f6cd8ca5f154e2
    }
} 