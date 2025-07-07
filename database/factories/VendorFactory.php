<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class VendorFactory extends Factory
{
    protected $model = \App\Models\Vendor::class;

    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'business_name' => $this->faker->company,
            'business_address' => $this->faker->address,
            'phone_number' => $this->faker->phoneNumber,
            'tax_id' => $this->faker->bothify('TAX#######'),
            'business_license' => $this->faker->bothify('LIC#######'),
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected', 'suspended']),
            'description' => $this->faker->sentence,
            'contact_person' => $this->faker->name,
            'contact_email' => $this->faker->safeEmail,
            'contact_phone' => $this->faker->phoneNumber,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
} 