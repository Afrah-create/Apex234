<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class YogurtProductFactory extends Factory
{
    protected $model = \App\Models\YogurtProduct::class;

    public function definition(): array
    {
        $products = [
            [
                'product_name' => 'Greek Vanilla Yoghurt',
                'product_code' => 'P0001',
                'flavor' => 'vanilla',
                'product_type' => 'greek',
                'selling_price' => 3500.00,
                'production_cost' => 2800.00,
            ],
            [
                'product_name' => 'Low Fat Blueberry Yoghurt',
                'product_code' => 'P0002',
                'flavor' => 'blueberry',
                'product_type' => 'low_fat',
                'selling_price' => 3000.00,
                'production_cost' => 2400.00,
            ],
            [
                'product_name' => 'Organic Strawberry Yoghurt',
                'product_code' => 'P0003',
                'flavor' => 'strawberry',
                'product_type' => 'organic',
                'selling_price' => 2500.00,
                'production_cost' => 2000.00,
            ],
        ];
        $product = $this->faker->randomElement($products);
        return array_merge([
            'production_facility_id' => 1,
            'description' => $this->faker->optional()->sentence(),
            'fat_content' => $this->faker->randomFloat(2, 0, 10),
            'protein_content' => $this->faker->randomFloat(2, 0, 10),
            'sugar_content' => $this->faker->randomFloat(2, 0, 20),
            'calories_per_100g' => $this->faker->numberBetween(50, 200),
            'package_size' => $this->faker->randomElement(['100g', '200g', '500g', '1kg']),
            'package_type' => $this->faker->randomElement(['cup', 'bottle', 'pouch']),
            'shelf_life_days' => $this->faker->numberBetween(7, 60),
            'storage_temperature' => $this->faker->randomFloat(2, 0, 10),
            'ingredients' => json_encode(['milk', 'bacterial culture', 'sugar']),
            'nutritional_info' => json_encode(['fat' => 3.5, 'protein' => 4.2]),
            'allergens' => json_encode(['milk']),
            'status' => 'active',
            'notes' => $this->faker->optional()->sentence(),
            'created_at' => now(),
            'updated_at' => now(),
        ], $product);
    }
} 