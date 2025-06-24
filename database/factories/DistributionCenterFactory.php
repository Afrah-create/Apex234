<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DistributionCenterFactory extends Factory
{
    protected $model = \App\Models\DistributionCenter::class;

    public function definition(): array
    {
        return [
            'center_name' => $this->faker->company() . ' Distribution Center',
            'center_code' => $this->faker->unique()->bothify('DC-#####'),
            'center_address' => $this->faker->address(),
            'center_phone' => $this->faker->phoneNumber(),
            'center_email' => $this->faker->unique()->safeEmail(),
            'center_manager' => $this->faker->name(),
            'manager_phone' => $this->faker->phoneNumber(),
            'manager_email' => $this->faker->unique()->safeEmail(),
            'center_type' => $this->faker->randomElement(['primary', 'secondary', 'regional', 'local']),
            'storage_capacity' => $this->faker->numberBetween(1000, 100000),
            'current_inventory' => $this->faker->numberBetween(0, 100000),
            'temperature_control' => $this->faker->randomFloat(2, 0, 10),
            'humidity_control' => $this->faker->optional()->randomFloat(2, 20, 90),
            'delivery_vehicles' => $this->faker->numberBetween(1, 50),
            'delivery_radius' => $this->faker->numberBetween(1, 500),
            'facilities' => json_encode(['cold storage', 'loading docks']),
            'certifications' => json_encode(['ISO 22000', 'HACCP']),
            'certification_status' => $this->faker->randomElement(['certified', 'pending', 'expired', 'suspended']),
            'last_inspection_date' => $this->faker->optional()->date(),
            'next_inspection_date' => $this->faker->optional()->date(),
            'status' => $this->faker->randomElement(['operational', 'maintenance', 'shutdown', 'suspended']),
            'notes' => $this->faker->optional()->sentence(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
} 