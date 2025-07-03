<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RawMaterial;
use App\Models\DairyFarm;
use App\Models\Supplier;
use App\Models\User;

class RawMaterialOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test supplier if it doesn't exist
        $supplierUser = User::firstOrCreate(
            ['email' => 'supplier@test.com'],
            [
                'name' => 'Test Supplier',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        $supplier = Supplier::firstOrCreate(
            ['user_id' => $supplierUser->id],
            [
                'company_name' => 'Test Dairy Farm',
                'registration_number' => 'REG123456',
                'business_address' => '123 Farm Road, Dairy City, State 12345',
                'contact_person' => 'John Supplier',
                'contact_phone' => '+1234567890',
                'contact_email' => 'supplier@test.com',
                'supplier_type' => 'dairy_farm',
                'status' => 'approved',
                'rating' => 4.5,
                'payment_terms_days' => 30,
            ]
        );

        // Create a dairy farm for the supplier
        $dairyFarm = DairyFarm::firstOrCreate(
            ['supplier_id' => $supplier->id],
            [
                'farm_name' => 'Test Dairy Farm',
                'farm_code' => 'FARM-001',
                'farm_address' => '123 Farm Road, Dairy City, State 12345',
                'farm_phone' => '+1234567890',
                'farm_email' => 'supplier@test.com',
                'farm_manager' => 'John Supplier',
                'manager_phone' => '+1234567890',
                'manager_email' => 'supplier@test.com',
                'total_cattle' => 50,
                'milking_cattle' => 30,
                'daily_milk_production' => 300.0,
                'certification_status' => 'certified',
                'certifications' => json_encode(['ISO 22000', 'HACCP']),
                'last_inspection_date' => now()->subMonths(1),
                'next_inspection_date' => now()->addMonths(11),
                'quality_rating' => 4.7,
                'status' => 'active',
                'notes' => 'Test farm for seeding',
            ]
        );

        // Create sample raw materials
        $materials = [
            [
                'material_name' => 'Fresh Milk',
                'material_type' => 'milk',
                'material_code' => 'MILK-001',
                'description' => 'Fresh organic milk from grass-fed cows',
                'quantity' => 500.0,
                'unit_of_measure' => 'liters',
                'unit_price' => 2.50,
                'harvest_date' => now()->subDays(1),
                'expiry_date' => now()->addDays(6),
                'quality_grade' => 'A',
                'status' => 'available',
            ],
            [
                'material_name' => 'White Sugar',
                'material_type' => 'sugar',
                'material_code' => 'SUGAR-001',
                'description' => 'Refined white sugar for yogurt production',
                'quantity' => 100.0,
                'unit_of_measure' => 'kg',
                'unit_price' => 1.20,
                'harvest_date' => now()->subDays(5),
                'expiry_date' => now()->addDays(365),
                'quality_grade' => 'A',
                'status' => 'available',
            ],
            [
                'material_name' => 'Strawberries',
                'material_type' => 'fruit',
                'material_code' => 'FRUIT-001',
                'description' => 'Fresh organic strawberries for flavoring',
                'quantity' => 50.0,
                'unit_of_measure' => 'kg',
                'unit_price' => 8.00,
                'harvest_date' => now()->subDays(2),
                'expiry_date' => now()->addDays(7),
                'quality_grade' => 'A',
                'status' => 'available',
            ],
        ];

        foreach ($materials as $materialData) {
            RawMaterial::firstOrCreate(
                ['material_code' => $materialData['material_code']],
                array_merge($materialData, [
                    'dairy_farm_id' => $dairyFarm->id,
                    'total_cost' => $materialData['quantity'] * $materialData['unit_price'],
                ])
            );
        }

        $this->command->info('Sample raw materials created successfully!');
        $this->command->info('Supplier: supplier@test.com / password');
    }
} 