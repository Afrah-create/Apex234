<?php

namespace Database\Seeders;

use App\Models\Supplier;
use App\Models\DairyFarm;
use App\Models\RawMaterial;
use Illuminate\Database\Seeder;

class DairyFarmAndRawMaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create a supplier first (required for dairy farm)
        $supplier = Supplier::firstOrCreate([
            'registration_number' => 'SUP001',
        ], [
            'user_id' => \App\Models\User::where('email', 'supplier@example.com')->first()->id ?? 1,
            'company_name' => 'Green Valley Dairy Supplies',
            'business_address' => '123 Dairy Lane, Kampala, Uganda',
            'contact_person' => 'John Dairy',
            'contact_phone' => '+256 701 234 567',
            'contact_email' => 'john@greenvalleydairy.com',
            'supplier_type' => 'dairy_farm',
            'status' => 'approved',
            'rating' => 4.5,
            'certifications' => json_encode(['ISO 9001', 'HACCP']),
            'verification_date' => now(),
            'contract_start_date' => now(),
            'contract_end_date' => now()->addYear(),
            'credit_limit' => 5000000, // 5 million UGX
            'payment_terms_days' => 30,
            'notes' => 'Reliable dairy farm supplier with excellent quality standards',
        ]);

        // Create one dairy farm
        $dairyFarm = DairyFarm::firstOrCreate([
            'farm_code' => 'DF001',
        ], [
            'supplier_id' => $supplier->id,
            'farm_name' => 'Green Valley Dairy Farm',
            'farm_address' => '456 Farm Road, Wakiso District, Uganda',
            'farm_phone' => '+256 702 345 678',
            'farm_email' => 'info@greenvalleydairyfarm.com',
            'farm_manager' => 'Sarah Muwonge',
            'manager_phone' => '+256 703 456 789',
            'manager_email' => 'sarah@greenvalleydairyfarm.com',
            'total_cattle' => 150,
            'milking_cattle' => 120,
            'daily_milk_production' => 1800.50, // 1800.5 liters per day
            'certification_status' => 'certified',
            'certifications' => json_encode(['ISO 22000', 'HACCP', 'Organic Certification']),
            'last_inspection_date' => now()->subDays(30),
            'next_inspection_date' => now()->addMonths(6),
            'quality_rating' => 4.8,
            'status' => 'active',
            'notes' => 'Premium quality dairy farm with organic certification. Excellent milk quality with high protein and fat content.',
        ]);

        // Create one raw material (fresh milk) from the dairy farm
        $rawMaterial = RawMaterial::firstOrCreate([
            'material_code' => 'RM001',
        ], [
            'dairy_farm_id' => $dairyFarm->id,
            'material_name' => 'Fresh Organic Milk',
            'material_type' => 'milk',
            'description' => 'Premium quality fresh organic milk from Green Valley Dairy Farm. High protein and fat content suitable for yogurt production.',
            'quantity' => 500.00, // 500 liters
            'unit_of_measure' => 'liters',
            'unit_price' => 2500.00, // 2500 UGX per liter
            'total_cost' => 1250000.00, // 1.25 million UGX
            'harvest_date' => now()->subDays(1),
            'expiry_date' => now()->addDays(7),
            'quality_grade' => 'A',
            'temperature' => 4.0, // 4°C storage temperature
            'ph_level' => 6.7,
            'fat_content' => 3.8, // 3.8% fat content
            'protein_content' => 3.2, // 3.2% protein content
            'status' => 'available',
            'quality_notes' => 'Excellent quality milk with optimal fat and protein content for yogurt production. Properly chilled and stored.',
        ]);

        $this->command->info('Successfully created:');
        $this->command->info('- 1 Dairy Farm: ' . $dairyFarm->farm_name);
        $this->command->info('- 1 Raw Material: ' . $rawMaterial->material_name);
    }
} 