<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function permissions()
    {
        return $this->roles()->with('permissions')->get()->pluck('permissions')->flatten()->unique('id');
    }

    public function getPrimaryRoleName()
    {
        $roles = $this->roles()->pluck('name')->toArray();
        $priority = ['admin', 'supplier', 'vendor', 'retailer', 'employee'];
        foreach ($priority as $role) {
            if (in_array($role, $roles)) {
                return $role;
            }
        }
        return null;
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\CustomResetPassword($token));
    }


    /**
     * Check if the user is approved (for vendor login).
     *
     * @return bool
     */
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function supplier()
    {
        return $this->hasOne(\App\Models\Supplier::class);
    }

    public function vendor()
    {
        return $this->hasOne(\App\Models\Vendor::class);
    }

    public function retailer()
    {
        return $this->hasOne(\App\Models\Retailer::class);
    }

    public function employee()
    {
        return $this->hasOne(\App\Models\Employee::class);
    }
}
    