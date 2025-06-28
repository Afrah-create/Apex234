<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\YogurtProduct;
use App\Models\ProductionFacility;

class YogurtProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create a production facility
        $facility = ProductionFacility::firstOrCreate(
            ['facility_name' => 'Main Production Facility'],
            [
                'facility_name' => 'Main Production Facility',
                'facility_code' => 'MPF001',
                'facility_address' => 'Kampala, Uganda',
                'facility_phone' => '+256-123-456-789',
                'facility_email' => 'production@caramel-yg.com',
                'facility_manager' => 'Production Manager',
                'manager_phone' => '+256-123-456-789',
                'manager_email' => 'manager@caramel-yg.com',
                'production_capacity' => 10000,
                'current_production' => 8000,
                'facility_type' => 'primary',
                'equipment' => json_encode(['pasteurizer', 'fermentation tanks', 'packaging line']),
                'certifications' => json_encode(['ISO 9001', 'HACCP']),
                'certification_status' => 'certified',
                'temperature_control' => 4.0,
                'humidity_control' => 60.0,
                'status' => 'operational'
            ]
        );

        // Create the three products from demand forecasting dataset
        $products = [
            [
                'production_facility_id' => $facility->id,
                'product_name' => 'Greek Vanilla Yoghurt',
                'product_code' => 'P0001',
                'description' => 'Premium Greek-style yogurt with natural vanilla flavor, rich and creamy texture.',
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
                'ingredients' => json_encode([
                    'Greek yogurt',
                    'Natural vanilla extract',
                    'Live active cultures',
                    'Vitamin D3'
                ]),
                'nutritional_info' => json_encode([
                    'fat' => 5.0,
                    'protein' => 8.5,
                    'carbohydrates' => 4.2,
                    'calcium' => 200,
                    'vitamin_d' => 2.5
                ]),
                'allergens' => json_encode(['milk']),
                'production_cost' => 2800.00, // UGX
                'selling_price' => 3500.00, // UGX - matches demand forecasting data
                'status' => 'active',
                'notes' => 'Premium product with high demand in Central and Northern regions'
            ],
            [
                'production_facility_id' => $facility->id,
                'product_name' => 'Low Fat Blueberry Yoghurt',
                'product_code' => 'P0002',
                'description' => 'Low-fat yogurt with real blueberry pieces, perfect for health-conscious consumers.',
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
                'ingredients' => json_encode([
                    'Low-fat yogurt',
                    'Blueberry pieces',
                    'Natural sweeteners',
                    'Live active cultures',
                    'Vitamin C'
                ]),
                'nutritional_info' => json_encode([
                    'fat' => 1.5,
                    'protein' => 6.8,
                    'carbohydrates' => 8.5,
                    'calcium' => 180,
                    'vitamin_c' => 15
                ]),
                'allergens' => json_encode(['milk']),
                'production_cost' => 2400.00, // UGX
                'selling_price' => 3000.00, // UGX - matches demand forecasting data
                'status' => 'active',
                'notes' => 'Popular choice across all regions, especially during promotions'
            ],
            [
                'production_facility_id' => $facility->id,
                'product_name' => 'Organic Strawberry Yoghurt',
                'product_code' => 'P0003',
                'description' => 'Certified organic yogurt with fresh strawberry flavor, made with organic ingredients.',
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
                'ingredients' => json_encode([
                    'Organic whole milk',
                    'Organic strawberries',
                    'Organic cane sugar',
                    'Live active cultures',
                    'Organic vanilla extract'
                ]),
                'nutritional_info' => json_encode([
                    'fat' => 3.2,
                    'protein' => 7.2,
                    'carbohydrates' => 6.8,
                    'calcium' => 220,
                    'fiber' => 1.2
                ]),
                'allergens' => json_encode(['milk']),
                'production_cost' => 2000.00, // UGX
                'selling_price' => 2500.00, // UGX - matches demand forecasting data
                'status' => 'active',
                'notes' => 'Organic premium product with growing demand in Eastern and Western regions'
            ]
        ];

        foreach ($products as $productData) {
            YogurtProduct::updateOrCreate(
                ['product_code' => $productData['product_code']],
                $productData
            );
        }

        $this->command->info('Yogurt products seeded successfully with UGX pricing!');
        $this->command->info('Products created:');
        $this->command->info('- Greek Vanilla Yoghurt: 3,500 UGX');
        $this->command->info('- Low Fat Blueberry Yoghurt: 3,000 UGX');
        $this->command->info('- Organic Strawberry Yoghurt: 2,500 UGX');
    }
} 