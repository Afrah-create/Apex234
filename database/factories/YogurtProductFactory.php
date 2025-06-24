<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class YogurtProductFactory extends Factory
{
    protected $model = \App\Models\YogurtProduct::class;

    public function definition(): array
    {
        return [
            'production_facility_id' => 1, // Should be set to a real facility in seeder
            'product_name' => $this->faker->word() . ' Yogurt',
            'product_code' => $this->faker->unique()->bothify('YOG-#####'),
            'description' => $this->faker->optional()->sentence(),
            'product_type' => $this->faker->randomElement(['plain', 'flavored', 'greek', 'low_fat', 'organic', 'probiotic']),
            'flavor' => $this->faker->optional()->randomElement(['strawberry', 'vanilla', 'mango', 'blueberry']),
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
            'production_cost' => $this->faker->randomFloat(2, 0.5, 5),
            'selling_price' => $this->faker->randomFloat(2, 1, 10),
            'status' => $this->faker->randomElement(['active', 'discontinued', 'seasonal', 'out_of_stock']),
            'notes' => $this->faker->optional()->sentence(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
} 