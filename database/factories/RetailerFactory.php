<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RetailerFactory extends Factory
{
    protected $model = \App\Models\Retailer::class;

    public function definition(): array
    {
        return [
            'user_id' => 1, // Should be set to a real user in seeder
            'store_name' => $this->faker->company(),
            'store_code' => $this->faker->unique()->bothify('STORE-####'),
            'store_address' => $this->faker->address(),
            'store_phone' => $this->faker->phoneNumber(),
            'store_email' => $this->faker->unique()->safeEmail(),
            'store_manager' => $this->faker->name(),
            'manager_phone' => $this->faker->phoneNumber(),
            'manager_email' => $this->faker->unique()->safeEmail(),
            'store_type' => $this->faker->randomElement(['supermarket', 'convenience_store', 'specialty_store', 'online', 'wholesale']),
            'store_size' => $this->faker->randomElement(['small', 'medium', 'large', 'extra_large']),
            'daily_customer_traffic' => $this->faker->numberBetween(50, 2000),
            'monthly_sales_volume' => $this->faker->randomFloat(2, 1000, 100000),
            'payment_methods' => json_encode(['cash', 'card', 'mobile']),
            'store_hours' => json_encode(['mon-fri' => '8:00-20:00', 'sat-sun' => '9:00-18:00']),
            'certification_status' => $this->faker->randomElement(['certified', 'pending', 'expired', 'suspended']),
            'certifications' => json_encode(['food safety', 'quality']),
            'last_inspection_date' => $this->faker->optional()->date(),
            'next_inspection_date' => $this->faker->optional()->date(),
            'customer_rating' => $this->faker->randomFloat(2, 0, 5),
            'status' => $this->faker->randomElement(['active', 'inactive', 'suspended', 'closed']),
            'notes' => $this->faker->optional()->sentence(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
} 