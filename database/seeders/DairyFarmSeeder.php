<?php

namespace Database\Seeders;

use App\Models\DairyFarm;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class DairyFarmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing suppliers or create one if none exist
        $suppliers = Supplier::all();
        
        if ($suppliers->isEmpty()) {
            // Create a supplier if none exists
            $supplier = Supplier::factory()->create();
        } else {
            $supplier = $suppliers->first();
        }

        // Create first dairy farm
        DairyFarm::create([
            'supplier_id' => $supplier->id,
            'farm_name' => 'Green Valley Dairy Farm',
            'farm_code' => 'GVDF001',
            'farm_address' => 'Plot 123, Green Valley Road, Kampala, Uganda',
            'farm_phone' => '+256-701-234-567',
            'farm_email' => 'info@greenvalleydairy.com',
            'farm_manager' => 'John Muwonge',
            'manager_phone' => '+256-702-345-678',
            'manager_email' => 'john.muwonge@greenvalleydairy.com',
            'total_cattle' => 150,
            'milking_cattle' => 120,
            'daily_milk_production' => 1800.50, // liters
            'certification_status' => 'certified',
            'certifications' => ['ISO 22000', 'HACCP', 'Organic'],
            'last_inspection_date' => '2024-01-15',
            'next_inspection_date' => '2024-07-15',
            'quality_rating' => 4.75,
            'status' => 'active',
            'notes' => 'Premium quality dairy farm with organic certification. Excellent milk quality standards maintained.',
        ]);

        // Create second dairy farm
        DairyFarm::create([
            'supplier_id' => $supplier->id,
            'farm_name' => 'Mountain View Dairy Farm',
            'farm_code' => 'MVDF002',
            'farm_address' => 'Plot 456, Mountain View Estate, Entebbe, Uganda',
            'farm_phone' => '+256-703-456-789',
            'farm_email' => 'contact@mountainviewdairy.com',
            'farm_manager' => 'Sarah Nakimera',
            'manager_phone' => '+256-704-567-890',
            'manager_email' => 'sarah.nakimera@mountainviewdairy.com',
            'total_cattle' => 200,
            'milking_cattle' => 160,
            'daily_milk_production' => 2400.00, // liters
            'certification_status' => 'certified',
            'certifications' => ['ISO 22000', 'HACCP', 'GAP'],
            'last_inspection_date' => '2024-02-20',
            'next_inspection_date' => '2024-08-20',
            'quality_rating' => 4.85,
            'status' => 'active',
            'notes' => 'Large-scale dairy operation with modern milking facilities. High production capacity with excellent quality control.',
        ]);

        $this->command->info('Dairy farms seeded successfully!');
    }
} 