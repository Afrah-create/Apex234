<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierFactory extends Factory
{
    protected $model = \App\Models\Supplier::class;

    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::inRandomOrder()->first()?->id ?? 1,
            'company_name' => $this->faker->company(),
            'registration_number' => $this->faker->unique()->bothify('REG-#####'),
            'business_address' => $this->faker->address(),
            'contact_person' => $this->faker->name(),
            'contact_phone' => $this->faker->phoneNumber(),
            'contact_email' => $this->faker->unique()->safeEmail(),
            'supplier_type' => $this->faker->randomElement(['dairy_farm', 'ingredient_supplier', 'packaging_supplier', 'equipment_supplier']),
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected', 'suspended']),
            'rating' => $this->faker->randomFloat(2, 0, 5),
            'certifications' => json_encode(['ISO 9001', 'HACCP']),
            'verification_date' => $this->faker->optional()->date(),
            'contract_start_date' => $this->faker->optional()->date(),
            'contract_end_date' => $this->faker->optional()->date(),
            'credit_limit' => $this->faker->randomFloat(2, 1000, 100000),
            'payment_terms_days' => $this->faker->randomElement([15, 30, 45, 60]),
            'notes' => $this->faker->optional()->sentence(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
} 