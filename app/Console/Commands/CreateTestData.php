<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\YogurtProduct;
use App\Models\Inventory;
use App\Models\ProductionFacility;
use App\Models\DistributionCenter;
use App\Models\Retailer;
use App\Models\Order;
use App\Models\User;

class CreateTestData extends Command
{
    protected $signature = 'create:test-data';
    protected $description = 'Create test data for inventory chart';

    public function handle()
    {
        $this->info('Creating test data...');

        // Create production facility
        $productionFacility = ProductionFacility::firstOrCreate([
            'facility_name' => 'Test Production Facility',
            'facility_code' => 'TPF001',
            'facility_address' => 'Test Address',
            'facility_phone' => '123-456-7890',
            'facility_email' => 'test@facility.com',
            'facility_manager' => 'Test Manager',
            'manager_phone' => '123-456-7891',
            'manager_email' => 'manager@facility.com',
            'production_capacity' => 1000,
            'current_production' => 500,
            'facility_type' => 'primary',
            'certification_status' => 'certified',
            'status' => 'operational'
        ]);

        // Create distribution center
        $distributionCenter = DistributionCenter::firstOrCreate([
            'center_name' => 'Test Distribution Center',
            'center_code' => 'TDC001',
            'center_address' => 'Test Address',
            'center_phone' => '123-456-7892',
            'center_email' => 'test@center.com',
            'center_manager' => 'Test Manager',
            'manager_phone' => '123-456-7893',
            'manager_email' => 'manager@center.com',
            'center_type' => 'primary',
            'storage_capacity' => 5000,
            'current_inventory' => 2000,
            'temperature_control' => 4.0,
            'humidity_control' => 60.0,
            'delivery_vehicles' => 5,
            'delivery_radius' => 50,
            'certification_status' => 'certified',
            'status' => 'operational'
        ]);

        // Create users for retailers
        $retailerUsers = [
            User::firstOrCreate([
                'email' => 'retailer1@example.com'
            ], [
                'name' => 'Retailer User 1',
                'password' => bcrypt('password')
            ]),
            User::firstOrCreate([
                'email' => 'retailer2@example.com'
            ], [
                'name' => 'Retailer User 2',
                'password' => bcrypt('password')
            ]),
            User::firstOrCreate([
                'email' => 'retailer3@example.com'
            ], [
                'name' => 'Retailer User 3',
                'password' => bcrypt('password')
            ]),
        ];

        // Create retailers
        $retailers = [
            Retailer::firstOrCreate([
                'user_id' => $retailerUsers[0]->id,
                'store_name' => 'Fresh Market Store',
                'store_code' => 'FMS001',
                'store_address' => '123 Main Street, City Center',
                'store_phone' => '555-0101',
                'store_email' => 'contact@freshmarket.com',
                'store_manager' => 'John Smith',
                'manager_phone' => '555-0102',
                'manager_email' => 'john@freshmarket.com',
                'store_type' => 'supermarket',
                'store_size' => 'large',
                'daily_customer_traffic' => 200,
                'monthly_sales_volume' => 50000.00,
                'certification_status' => 'certified',
                'status' => 'active'
            ]),
            Retailer::firstOrCreate([
                'user_id' => $retailerUsers[1]->id,
                'store_name' => 'Health Foods Plus',
                'store_code' => 'HFP002',
                'store_address' => '456 Health Avenue, Wellness District',
                'store_phone' => '555-0201',
                'store_email' => 'info@healthfoodsplus.com',
                'store_manager' => 'Sarah Johnson',
                'manager_phone' => '555-0202',
                'manager_email' => 'sarah@healthfoodsplus.com',
                'store_type' => 'specialty_store',
                'store_size' => 'medium',
                'daily_customer_traffic' => 150,
                'monthly_sales_volume' => 30000.00,
                'certification_status' => 'certified',
                'status' => 'active'
            ]),
            Retailer::firstOrCreate([
                'user_id' => $retailerUsers[2]->id,
                'store_name' => 'Organic Corner',
                'store_code' => 'OC003',
                'store_address' => '789 Organic Lane, Green Zone',
                'store_phone' => '555-0301',
                'store_email' => 'hello@organiccorner.com',
                'store_manager' => 'Mike Wilson',
                'manager_phone' => '555-0302',
                'manager_email' => 'mike@organiccorner.com',
                'store_type' => 'specialty_store',
                'store_size' => 'small',
                'daily_customer_traffic' => 100,
                'monthly_sales_volume' => 20000.00,
                'certification_status' => 'certified',
                'status' => 'active'
            ])
        ];

        // Create yogurt products (using the three main products from demand forecasting)
        $products = [
            [
                'product_name' => 'Greek Vanilla Yoghurt',
                'product_code' => 'P0001',
                'product_type' => 'greek',
                'flavor' => 'vanilla',
                'fat_content' => 5.0,
                'protein_content' => 8.5,
                'sugar_content' => 4.2,
                'calories_per_100g' => 120,
                'package_size' => '500g',
                'package_type' => 'cup',
                'shelf_life_days' => 21,
                'storage_temperature' => 4.0,
                'production_cost' => 2800.00,
                'selling_price' => 3500.00, // UGX
                'status' => 'active'
            ],
            [
                'product_name' => 'Low Fat Blueberry Yoghurt',
                'product_code' => 'P0002',
                'product_type' => 'low_fat',
                'flavor' => 'blueberry',
                'fat_content' => 1.5,
                'protein_content' => 6.8,
                'sugar_content' => 8.5,
                'calories_per_100g' => 95,
                'package_size' => '500g',
                'package_type' => 'cup',
                'shelf_life_days' => 18,
                'storage_temperature' => 4.0,
                'production_cost' => 2400.00,
                'selling_price' => 3000.00, // UGX
                'status' => 'active'
            ],
            [
                'product_name' => 'Organic Strawberry Yoghurt',
                'product_code' => 'P0003',
                'product_type' => 'organic',
                'flavor' => 'strawberry',
                'fat_content' => 3.2,
                'protein_content' => 7.2,
                'sugar_content' => 6.8,
                'calories_per_100g' => 110,
                'package_size' => '500g',
                'package_type' => 'cup',
                'shelf_life_days' => 15,
                'storage_temperature' => 4.0,
                'production_cost' => 2000.00,
                'selling_price' => 2500.00, // UGX
                'status' => 'active'
            ]
        ];

        foreach ($products as $productData) {
            $product = YogurtProduct::firstOrCreate([
                'product_code' => $productData['product_code']
            ], array_merge($productData, [
                'production_facility_id' => $productionFacility->id
            ]));

            // Create inventory for each product
            Inventory::firstOrCreate([
                'yogurt_product_id' => $product->id,
                'distribution_center_id' => $distributionCenter->id,
                'batch_number' => 'BATCH-' . $product->id . '-001'
            ], [
                'quantity_available' => rand(50, 200),
                'quantity_reserved' => rand(10, 50),
                'quantity_damaged' => rand(0, 5),
                'quantity_expired' => rand(0, 3),
                'production_date' => now()->subDays(rand(1, 10)),
                'expiry_date' => now()->addDays(rand(5, 20)),
                'storage_temperature' => 4.0,
                'storage_location' => 'cold_room',
                'shelf_location' => 'A1-' . $product->id,
                'inventory_status' => 'available',
                'unit_cost' => $product->production_cost,
                'total_value' => $product->production_cost * rand(50, 200),
                'last_updated' => now(),
                'notes' => 'Test inventory data'
            ]);
        }

        // Create sample orders with different statuses
        $orderStatuses = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'];
        $paymentStatuses = ['pending', 'paid', 'failed', 'refunded'];
        $orderTypes = ['regular', 'rush', 'bulk', 'seasonal'];

        for ($i = 1; $i <= 15; $i++) {
            $retailer = $retailers[array_rand($retailers)];
            $status = $orderStatuses[array_rand($orderStatuses)];
            $paymentStatus = $paymentStatuses[array_rand($paymentStatuses)];
            $orderType = $orderTypes[array_rand($orderTypes)];
            
            $subtotal = rand(100, 500);
            $taxAmount = $subtotal * 0.08;
            $shippingCost = rand(10, 30);
            $discountAmount = rand(0, 50);
            $totalAmount = $subtotal + $taxAmount + $shippingCost - $discountAmount;

            $order = Order::firstOrCreate([
                'order_number' => 'ORD-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'retailer_id' => $retailer->id,
                'distribution_center_id' => $distributionCenter->id,
                'order_date' => now()->subDays(rand(1, 30)),
                'requested_delivery_date' => now()->addDays(rand(1, 7)),
                'actual_delivery_date' => $status === 'delivered' ? now()->subDays(rand(1, 5)) : null,
                'order_type' => $orderType,
                'order_status' => $status,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'shipping_cost' => $shippingCost,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'payment_method' => 'credit_card',
                'payment_status' => $paymentStatus,
                'delivery_address' => $retailer->store_address,
                'delivery_contact' => $retailer->store_manager,
                'delivery_phone' => $retailer->store_phone,
                'special_instructions' => rand(0, 1) ? 'Handle with care - fragile items' : null,
                'notes' => 'Sample order for testing purposes'
            ]);
        }

        $this->info('Test data created successfully!');
        $this->info('Created ' . count($products) . ' products with inventory data.');
        $this->info('Created 15 sample orders with various statuses.');
    }
} 