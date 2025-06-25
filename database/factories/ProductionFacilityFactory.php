<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductionFacilityFactory extends Factory
{
    protected $model = \App\Models\ProductionFacility::class;

    public function definition(): array
    {
        return [
            'facility_name' => $this->faker->company() . ' Facility',
            'facility_code' => $this->faker->unique()->bothify('PF-#####'),
            'facility_address' => $this->faker->address(),
            'facility_phone' => $this->faker->phoneNumber(),
            'facility_email' => $this->faker->unique()->safeEmail(),
            'facility_manager' => $this->faker->name(),
            'manager_phone' => $this->faker->phoneNumber(),
            'manager_email' => $this->faker->unique()->safeEmail(),
            'production_capacity' => $this->faker->numberBetween(1000, 100000),
            'current_production' => $this->faker->numberBetween(0, 100000),
            'facility_type' => $this->faker->randomElement(['primary', 'secondary', 'packaging', 'storage']),
            'equipment' => json_encode(['pasteurizer', 'fermenter', 'packager']),
            'certifications' => json_encode(['ISO 22000', 'HACCP']),
            'certification_status' => $this->faker->randomElement(['certified', 'pending', 'expired', 'suspended']),
            'last_inspection_date' => $this->faker->optional()->date(),
            'next_inspection_date' => $this->faker->optional()->date(),
            'temperature_control' => $this->faker->optional()->randomFloat(2, 0, 10),
            'humidity_control' => $this->faker->optional()->randomFloat(2, 20, 90),
            'status' => $this->faker->randomElement(['operational', 'maintenance', 'shutdown', 'suspended']),
            'notes' => $this->faker->optional()->sentence(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
} 