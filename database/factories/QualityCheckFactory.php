<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class QualityCheckFactory extends Factory
{
    protected $model = \App\Models\QualityCheck::class;

    public function definition(): array
    {
        return [
            'yogurt_product_id' => 1, // Should be set to a real product in seeder
            'production_facility_id' => 1, // Should be set to a real facility in seeder
            'batch_number' => $this->faker->unique()->bothify('BATCH-#####'),
            'production_date' => $this->faker->date(),
            'expiry_date' => $this->faker->date(),
            'ph_level' => $this->faker->randomFloat(1, 4.0, 4.6),
            'temperature' => $this->faker->randomFloat(2, 0, 10),
            'fat_content' => $this->faker->randomFloat(2, 0, 10),
            'protein_content' => $this->faker->randomFloat(2, 0, 10),
            'moisture_content' => $this->faker->optional()->randomFloat(2, 0, 100),
            'total_solids' => $this->faker->optional()->randomFloat(2, 0, 100),
            'bacteria_count' => $this->faker->optional()->numberBetween(0, 10000),
            'yeast_count' => $this->faker->optional()->numberBetween(0, 1000),
            'mold_count' => $this->faker->optional()->numberBetween(0, 1000),
            'consistency' => $this->faker->randomElement(['smooth', 'lumpy', 'watery', 'thick']),
            'color' => $this->faker->randomElement(['white', 'off_white', 'yellowish', 'other']),
            'taste' => $this->faker->randomElement(['good', 'acceptable', 'poor', 'unacceptable']),
            'odor' => $this->faker->randomElement(['normal', 'slight_off', 'strong_off', 'unacceptable']),
            'overall_quality' => $this->faker->randomElement(['excellent', 'good', 'acceptable', 'poor', 'rejected']),
            'inspector_name' => $this->faker->name(),
            'notes' => $this->faker->optional()->sentence(),
            'test_results' => json_encode(['ph' => 4.2, 'fat' => 3.5]),
            'status' => $this->faker->randomElement(['pending', 'passed', 'failed', 'retest']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
} 