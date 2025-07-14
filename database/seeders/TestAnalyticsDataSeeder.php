<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Retailer;
use App\Models\DistributionCenter;
use App\Models\YogurtProduct;
use App\Models\User;
use Carbon\Carbon;

class TestAnalyticsDataSeeder extends Seeder
{
    public function run()
    {
        // Get existing data
        $retailers = Retailer::all();
        $dc = DistributionCenter::first();
        $products = YogurtProduct::all();
        $users = User::all();

        if ($retailers->isEmpty() || !$dc || $products->isEmpty()) {
            $this->command->error('Required data not found. Please run other seeders first.');
            return;
        }

        // Create test orders for multiple retailers
        $orderCount = 25;
        for ($i = 0; $i < $orderCount; $i++) {
            $retailer = $retailers[$i % $retailers->count()];
            $order = Order::create([
                'retailer_id' => $retailer->id,
                'distribution_center_id' => $dc->id,
                'order_number' => 'ORD-' . rand(10000, 99999),
                'order_date' => Carbon::now()->subDays(rand(1, 90)),
                'requested_delivery_date' => Carbon::now()->addDays(rand(1, 7)),
                'order_type' => ['regular', 'rush', 'bulk', 'seasonal'][rand(0, 3)],
                'order_status' => ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'][rand(0, 5)],
                'subtotal' => rand(100, 1000),
                'tax_amount' => rand(10, 100),
                'shipping_cost' => rand(5, 50),
                'discount_amount' => rand(0, 50),
                'total_amount' => rand(150, 1200),
                'payment_method' => 'credit_card',
                'payment_status' => 'paid',
                'delivery_address' => 'Test Address ' . ($i + 1),
                'delivery_contact' => 'Test Contact ' . ($i + 1),
                'delivery_phone' => '+1234567890',
                'special_instructions' => 'Test instructions',
                'notes' => 'Test order ' . ($i + 1)
            ]);

            // Create order items for each order
            foreach ($products as $product) {
                $totalPrice = rand(10, 500);
                $productionDate = Carbon::now()->subDays(rand(1, 90));
                $expiryDate = (clone $productionDate)->addDays(14);
                \App\Models\OrderItem::create([
                    'order_id' => $order->id,
                    'yogurt_product_id' => $product->id,
                    'quantity' => rand(1, 10),
                    'unit_price' => rand(10, 50),
                    'total_price' => $totalPrice,
                    'final_price' => $totalPrice,
                    'production_date' => $productionDate,
                    'expiry_date' => $expiryDate,
                ]);
            }
        }

        // Update product names if they're null
        $products->each(function ($product, $index) {
            if (!$product->product_name) {
                $product->update([
                    'product_name' => 'Yogurt Product ' . ($index + 1)
                ]);
            }
        });

        $this->command->info('Created 25 test orders with order items and updated product names.');
    }
} 