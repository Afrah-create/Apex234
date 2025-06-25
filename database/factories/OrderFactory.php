<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = \App\Models\Order::class;

    public function definition(): array
    {
        return [
            'retailer_id' => 1, // Should be set to a real retailer in seeder
            'distribution_center_id' => 1, // Should be set to a real distribution center in seeder
            'order_number' => $this->faker->unique()->bothify('ORD-#####'),
            'order_date' => $this->faker->date(),
            'requested_delivery_date' => $this->faker->date(),
            'actual_delivery_date' => $this->faker->optional()->date(),
            'order_type' => $this->faker->randomElement(['regular', 'rush', 'bulk', 'seasonal']),
            'order_status' => $this->faker->randomElement(['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled']),
            'subtotal' => $this->faker->randomFloat(2, 100, 10000),
            'tax_amount' => $this->faker->randomFloat(2, 0, 1000),
            'shipping_cost' => $this->faker->randomFloat(2, 0, 500),
            'discount_amount' => $this->faker->randomFloat(2, 0, 500),
            'total_amount' => $this->faker->randomFloat(2, 100, 12000),
            'payment_method' => $this->faker->randomElement(['cash', 'card', 'mobile']),
            'payment_status' => $this->faker->randomElement(['pending', 'paid', 'failed', 'refunded']),
            'delivery_address' => $this->faker->address(),
            'delivery_contact' => $this->faker->name(),
            'delivery_phone' => $this->faker->phoneNumber(),
            'special_instructions' => $this->faker->optional()->sentence(),
            'notes' => $this->faker->optional()->sentence(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
} 