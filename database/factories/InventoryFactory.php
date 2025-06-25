<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryFactory extends Factory
{
    protected $model = \App\Models\Inventory::class;

    public function definition(): array
    {
        return [
            'yogurt_product_id' => 1, // Should be set to a real product in seeder
            'distribution_center_id' => 1, // Should be set to a real center in seeder
            'quality_check_id' => null, // Can be set to a real quality check in seeder
            'batch_number' => $this->faker->unique()->bothify('BATCH-#####'),
            'quantity_available' => $this->faker->numberBetween(0, 1000),
            'quantity_reserved' => $this->faker->numberBetween(0, 500),
            'quantity_damaged' => $this->faker->numberBetween(0, 50),
            'quantity_expired' => $this->faker->numberBetween(0, 50),
            'production_date' => $this->faker->date(),
            'expiry_date' => $this->faker->date(),
            'storage_temperature' => $this->faker->randomFloat(2, 0, 10),
            'storage_location' => $this->faker->randomElement(['cold_room', 'refrigerator', 'freezer', 'warehouse']),
            'shelf_location' => $this->faker->optional()->bothify('SHELF-##'),
            'inventory_status' => $this->faker->randomElement(['available', 'low_stock', 'out_of_stock', 'expired', 'damaged']),
            'unit_cost' => $this->faker->randomFloat(2, 1, 100),
            'total_value' => $this->faker->randomFloat(2, 100, 10000),
            'last_updated' => $this->faker->date(),
            'notes' => $this->faker->optional()->sentence(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
} 