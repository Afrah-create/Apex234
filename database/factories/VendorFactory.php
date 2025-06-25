<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class VendorFactory extends Factory
{
    protected $model = \App\Models\Vendor::class;

    public function definition(): array
    {
        return [
            // Add fields as needed
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
} 