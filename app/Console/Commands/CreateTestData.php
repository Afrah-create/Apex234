<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\YogurtProduct;
use App\Models\Inventory;
use App\Models\ProductionFacility;
use App\Models\DistributionCenter;

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

        // Create yogurt products
        $products = [
            [
                'product_name' => 'Plain Greek Yogurt',
                'product_code' => 'PGY001',
                'product_type' => 'greek',
                'fat_content' => 5.0,
                'protein_content' => 15.0,
                'sugar_content' => 3.0,
                'calories_per_100g' => 120,
                'package_size' => '500g',
                'package_type' => 'cup',
                'shelf_life_days' => 21,
                'storage_temperature' => 4.0,
                'production_cost' => 2.50,
                'selling_price' => 4.99,
                'status' => 'active'
            ],
            [
                'product_name' => 'Strawberry Yogurt',
                'product_code' => 'SY002',
                'product_type' => 'flavored',
                'flavor' => 'strawberry',
                'fat_content' => 3.5,
                'protein_content' => 12.0,
                'sugar_content' => 8.0,
                'calories_per_100g' => 110,
                'package_size' => '150g',
                'package_type' => 'cup',
                'shelf_life_days' => 14,
                'storage_temperature' => 4.0,
                'production_cost' => 1.80,
                'selling_price' => 2.99,
                'status' => 'active'
            ],
            [
                'product_name' => 'Vanilla Low-Fat Yogurt',
                'product_code' => 'VLY003',
                'product_type' => 'low_fat',
                'flavor' => 'vanilla',
                'fat_content' => 2.0,
                'protein_content' => 10.0,
                'sugar_content' => 6.0,
                'calories_per_100g' => 90,
                'package_size' => '200g',
                'package_type' => 'cup',
                'shelf_life_days' => 18,
                'storage_temperature' => 4.0,
                'production_cost' => 1.60,
                'selling_price' => 3.49,
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

        $this->info('Test data created successfully!');
        $this->info('Created ' . count($products) . ' products with inventory data.');
    }
} 