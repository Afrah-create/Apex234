<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DeliveryFactory extends Factory
{
    protected $model = \App\Models\Delivery::class;

    public function definition(): array
    {
        return [
            'order_id' => 1, // Should be set to a real order in seeder
            'distribution_center_id' => 1, // Should be set to a real center in seeder
            'retailer_id' => 1, // Should be set to a real retailer in seeder
            'vendor_id' => \App\Models\Vendor::inRandomOrder()->first()?->id ?? \App\Models\Vendor::factory()->create()->id,
            'delivery_number' => $this->faker->unique()->bothify('DEL-#####'),
            'vehicle_number' => $this->faker->optional()->bothify('VEH-####'),
            'driver_name' => $this->faker->name(),
            'driver_phone' => $this->faker->phoneNumber(),
            'driver_license' => $this->faker->optional()->bothify('DL-#####'),
            'scheduled_delivery_date' => $this->faker->date(),
            'scheduled_delivery_time' => $this->faker->time(),
            'actual_delivery_date' => $this->faker->optional()->date(),
            'actual_delivery_time' => $this->faker->optional()->time(),
            'delivery_status' => $this->faker->randomElement(['scheduled', 'in_transit', 'out_for_delivery', 'delivered', 'failed', 'cancelled']),
            'delivery_temperature' => $this->faker->optional()->randomFloat(2, 0, 10),
            'temperature_status' => $this->faker->optional()->randomElement(['maintained', 'fluctuated', 'exceeded_limit']),
            'delivery_duration' => $this->faker->optional()->numberBetween(10, 300),
            'delivery_distance' => $this->faker->optional()->randomFloat(2, 1, 500),
            'fuel_consumption' => $this->faker->optional()->randomFloat(2, 1, 100),
            'delivery_address' => $this->faker->address(),
            'recipient_name' => $this->faker->name(),
            'recipient_phone' => $this->faker->phoneNumber(),
            'recipient_signature' => $this->faker->optional()->sha1(),
            'delivery_notes' => $this->faker->optional()->sentence(),
            'customer_satisfaction' => $this->faker->optional()->randomElement(['excellent', 'good', 'fair', 'poor']),
            'customer_feedback' => $this->faker->optional()->sentence(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
} 