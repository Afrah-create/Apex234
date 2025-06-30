<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Inventory;
use App\Models\YogurtProduct;
use App\Models\DistributionCenter;
use App\Models\User;
use App\Models\Role;
use App\Models\Vendor;
use App\Models\Supplier;
use App\Models\Retailer;
use App\Models\DairyFarm;
use App\Models\RawMaterial;
use App\Models\ProductionFacility;
use App\Models\QualityCheck;
use App\Models\Delivery;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ReportTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample users and roles
        $this->createSampleUsers();
        
        // Create sample orders for the last 90 days
        $this->createSampleOrders();
        
        // Create sample inventory data
        $this->createSampleInventory();
        
        // Create sample quality checks
        $this->createSampleQualityChecks();
        
        // Create sample deliveries
        $this->createSampleDeliveries();
        
        $this->command->info('Comprehensive report test data seeded successfully!');
    }

    private function createSampleUsers()
    {
        // Create roles if they don't exist
        $roles = ['admin', 'vendor', 'supplier', 'retailer'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // Create sample users for each role
        $users = [
            ['name' => 'Test Vendor', 'email' => 'vendor@example.com', 'role' => 'vendor'],
            ['name' => 'Test Supplier', 'email' => 'supplier@example.com', 'role' => 'supplier'],
            ['name' => 'Test Retailer', 'email' => 'retailer@example.com', 'role' => 'retailer'],
            ['name' => 'Another Vendor', 'email' => 'vendor2@example.com', 'role' => 'vendor'],
            ['name' => 'Another Supplier', 'email' => 'supplier2@example.com', 'role' => 'supplier'],
            ['name' => 'Another Retailer', 'email' => 'retailer2@example.com', 'role' => 'retailer'],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                ]
            );
            
            $role = Role::where('name', $userData['role'])->first();
            if ($role && !$user->roles()->where('name', $userData['role'])->exists()) {
                $user->roles()->attach($role);
            }
        }
    }

    private function createSampleOrders()
    {
        // Check if we have the required data
        $retailers = Retailer::all();
        $distributionCenters = DistributionCenter::all();
        
        if ($retailers->isEmpty() || $distributionCenters->isEmpty()) {
            $this->command->warn('No retailers or distribution centers found. Creating basic test data...');
            $this->createBasicTestData();
            $retailers = Retailer::all();
            $distributionCenters = DistributionCenter::all();
        }

        $products = YogurtProduct::all();
        if ($products->isEmpty()) {
            $this->command->warn('No yogurt products found. Skipping order creation.');
            return;
        }

        // Create orders for the last 90 days with more variety
        for ($i = 0; $i < 50; $i++) {
            $orderDate = Carbon::now()->subDays(rand(0, 90));
            $retailer = $retailers->random();
            $distributionCenter = $distributionCenters->random();
            $product = $products->random();
            
            $quantity = rand(10, 200);
            $unitPrice = $product->selling_price ?? 5000;
            $subtotal = $quantity * $unitPrice;
            $taxAmount = $subtotal * 0.18; // 18% tax
            $shippingCost = rand(5000, 15000);
            $discountAmount = $subtotal * (rand(0, 10) / 100); // 0-10% discount
            $totalAmount = $subtotal + $taxAmount + $shippingCost - $discountAmount;
            
            Order::create([
                'retailer_id' => $retailer->id,
                'distribution_center_id' => $distributionCenter->id,
                'order_number' => 'ORD' . date('Ymd') . strtoupper(Str::random(6)),
                'order_date' => $orderDate,
                'requested_delivery_date' => $orderDate->copy()->addDays(rand(1, 7)),
                'actual_delivery_date' => $orderDate->copy()->addDays(rand(1, 7)),
                'order_type' => $this->getRandomOrderType(),
                'order_status' => $this->getRandomOrderStatus(),
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'shipping_cost' => $shippingCost,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'payment_method' => $this->getRandomPaymentMethod(),
                'payment_status' => $this->getRandomPaymentStatus(),
                'delivery_address' => 'Sample Address ' . ($i + 1) . ', Kampala, Uganda',
                'delivery_contact' => 'Contact Person ' . ($i + 1),
                'delivery_phone' => '+256' . rand(700000000, 799999999),
                'special_instructions' => 'Handle with care',
                'notes' => 'Sample order for testing reports',
                'created_at' => $orderDate,
                'updated_at' => $orderDate
            ]);
        }
    }

    private function createSampleInventory()
    {
        $products = YogurtProduct::all();
        $distributionCenters = DistributionCenter::all();
        
        if ($products->isEmpty() || $distributionCenters->isEmpty()) {
            $this->command->warn('No products or distribution centers found. Skipping inventory creation.');
            return;
        }

        // Create multiple inventory records for each product
        foreach ($products as $product) {
            for ($i = 0; $i < 3; $i++) { // Create 3 inventory records per product
                $distributionCenter = $distributionCenters->random();
                $quantityAvailable = rand(50, 500);
                $quantityReserved = rand(0, 50);
                $quantityDamaged = rand(0, 10);
                $quantityExpired = rand(0, 5);
                $unitCost = $product->cost_price ?? 1000;
                $totalValue = ($quantityAvailable + $quantityReserved) * $unitCost;
                
                Inventory::create([
                    'yogurt_product_id' => $product->id,
                    'distribution_center_id' => $distributionCenter->id,
                    'batch_number' => 'BATCH' . date('Ymd') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
                    'quantity_available' => $quantityAvailable,
                    'quantity_reserved' => $quantityReserved,
                    'quantity_damaged' => $quantityDamaged,
                    'quantity_expired' => $quantityExpired,
                    'production_date' => Carbon::now()->subDays(rand(1, 30)),
                    'expiry_date' => Carbon::now()->addDays(rand(30, 90)),
                    'storage_temperature' => rand(-5, 8),
                    'storage_location' => $this->getRandomStorageLocation(),
                    'shelf_location' => 'Shelf ' . rand(1, 10) . '-' . rand(1, 5),
                    'inventory_status' => $this->getRandomInventoryStatus(),
                    'unit_cost' => $unitCost,
                    'total_value' => $totalValue,
                    'last_updated' => Carbon::now()->subDays(rand(0, 30)),
                    'notes' => 'Sample inventory data for testing reports'
                ]);
            }
        }
    }

    private function createSampleQualityChecks()
    {
        $products = YogurtProduct::all();
        $productionFacilities = ProductionFacility::all();
        
        if ($products->isEmpty()) {
            $this->command->warn('No products found. Skipping quality check creation.');
            return;
        }

        // Create sample quality checks
        for ($i = 0; $i < 30; $i++) {
            $product = $products->random();
            $facility = $productionFacilities->random();
            $checkDate = Carbon::now()->subDays(rand(0, 60));
            
            QualityCheck::create([
                'yogurt_product_id' => $product->id,
                'production_facility_id' => $facility->id,
                'batch_number' => 'QC' . date('Ymd') . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'check_date' => $checkDate,
                'check_type' => $this->getRandomCheckType(),
                'ph_level' => rand(40, 70) / 10, // 4.0 to 7.0
                'temperature' => rand(35, 45) / 10, // 3.5 to 4.5
                'fat_content' => rand(20, 50) / 10, // 2.0 to 5.0
                'protein_content' => rand(30, 60) / 10, // 3.0 to 6.0
                'bacterial_count' => rand(1000, 10000),
                'shelf_life_days' => rand(7, 30),
                'taste_score' => rand(70, 100),
                'texture_score' => rand(70, 100),
                'appearance_score' => rand(70, 100),
                'overall_score' => rand(70, 100),
                'pass_fail_status' => $this->getRandomPassFailStatus(),
                'inspector_name' => 'Inspector ' . ($i + 1),
                'notes' => 'Sample quality check for testing reports',
                'created_at' => $checkDate,
                'updated_at' => $checkDate
            ]);
        }
    }

    private function createSampleDeliveries()
    {
        $orders = Order::all();
        $distributionCenters = DistributionCenter::all();
        
        if ($orders->isEmpty() || $distributionCenters->isEmpty()) {
            $this->command->warn('No orders or distribution centers found. Skipping delivery creation.');
            return;
        }

        // Create sample deliveries
        for ($i = 0; $i < 25; $i++) {
            $order = $orders->random();
            $distributionCenter = $distributionCenters->random();
            $deliveryDate = Carbon::now()->subDays(rand(0, 30));
            
            Delivery::create([
                'order_id' => $order->id,
                'distribution_center_id' => $distributionCenter->id,
                'delivery_number' => 'DEL' . date('Ymd') . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'delivery_date' => $deliveryDate,
                'delivery_status' => $this->getRandomDeliveryStatus(),
                'delivery_method' => $this->getRandomDeliveryMethod(),
                'vehicle_number' => 'UG' . rand(100, 999) . 'A',
                'driver_name' => 'Driver ' . ($i + 1),
                'driver_phone' => '+256' . rand(700000000, 799999999),
                'delivery_address' => $order->delivery_address,
                'delivery_contact' => $order->delivery_contact,
                'delivery_phone' => $order->delivery_phone,
                'estimated_delivery_time' => $deliveryDate->copy()->addHours(rand(1, 4)),
                'actual_delivery_time' => $deliveryDate->copy()->addHours(rand(1, 4)),
                'delivery_distance_km' => rand(5, 50),
                'fuel_cost' => rand(5000, 15000),
                'delivery_cost' => rand(10000, 25000),
                'customer_signature' => 'Customer ' . ($i + 1),
                'delivery_notes' => 'Sample delivery for testing reports',
                'created_at' => $deliveryDate,
                'updated_at' => $deliveryDate
            ]);
        }
    }

    private function createBasicTestData()
    {
        // Create a basic distribution center if none exists
        if (DistributionCenter::where('center_code', 'DC001')->doesntExist()) {
            DistributionCenter::create([
                'center_name' => 'Main Distribution Center',
                'center_code' => 'DC001',
                'center_address' => 'Kampala, Uganda',
                'center_phone' => '+256700000000',
                'center_email' => 'dc@example.com',
                'center_manager' => 'Manager',
                'manager_phone' => '+256700000001',
                'manager_email' => 'manager@example.com',
                'center_type' => 'primary',
                'storage_capacity' => 5000,
                'current_inventory' => 1000,
                'temperature_control' => 4.0,
                'humidity_control' => 60.0,
                'delivery_vehicles' => 5,
                'delivery_radius' => 50,
                'facilities' => json_encode(['cold storage', 'loading docks']),
                'certifications' => json_encode(['ISO 22000']),
                'certification_status' => 'certified',
                'last_inspection_date' => now()->subMonths(2),
                'next_inspection_date' => now()->addMonths(10),
                'status' => 'operational',
                'notes' => 'Main DC for Kampala',
            ]);
        }

        // Create a basic retailer if none exists
        if (Retailer::where('store_code', 'STORE001')->doesntExist()) {
            $user = User::where('email', 'retailer@example.com')->first();
            if (!$user) {
                $user = User::factory()->create([
                    'name' => 'Test Retailer',
                    'email' => 'retailer@example.com',
                    'password' => bcrypt('password')
                ]);
                $user->roles()->attach(Role::where('name', 'retailer')->first());
            }
            Retailer::create([
                'user_id' => $user->id,
                'store_name' => 'Test Store',
                'store_code' => 'STORE001',
                'store_address' => 'Test Address, Kampala, Uganda',
                'store_phone' => '+256700000002',
                'store_email' => 'store@example.com',
                'store_manager' => 'Test Manager',
                'manager_phone' => '+256700000003',
                'manager_email' => 'manager@example.com',
                'store_type' => 'supermarket',
                'store_size' => 'medium',
                'daily_customer_traffic' => 200,
                'monthly_sales_volume' => 10000000,
                'payment_methods' => json_encode(['cash', 'card', 'mobile']),
                'store_hours' => json_encode(['mon-fri' => '8:00-20:00', 'sat-sun' => '9:00-18:00']),
                'certification_status' => 'certified',
                'certifications' => json_encode(['ISO 9001']),
                'last_inspection_date' => now()->subMonths(1),
                'next_inspection_date' => now()->addMonths(11),
                'customer_rating' => 4.5,
                'status' => 'active',
                'notes' => 'Main test retailer',
            ]);
        }

        // Create a basic production facility if none exists
        if (ProductionFacility::where('facility_code', 'PF001')->doesntExist()) {
            ProductionFacility::create([
                'facility_name' => 'Main Production Facility',
                'facility_code' => 'PF001',
                'facility_address' => 'Industrial Area, Kampala, Uganda',
                'facility_phone' => '+256700000004',
                'facility_email' => 'production@example.com',
                'facility_manager' => 'Production Manager',
                'manager_phone' => '+256700000005',
                'manager_email' => 'prodmanager@example.com',
                'facility_type' => 'yogurt_processing',
                'production_capacity' => 10000,
                'daily_production' => 5000,
                'equipment_list' => json_encode(['pasteurizer', 'fermenter', 'packaging']),
                'certifications' => json_encode(['HACCP', 'ISO 22000']),
                'certification_status' => 'certified',
                'last_inspection_date' => now()->subMonths(3),
                'next_inspection_date' => now()->addMonths(9),
                'status' => 'operational',
                'notes' => 'Main production facility',
            ]);
        }
    }

    private function getRandomOrderStatus()
    {
        $statuses = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'];
        return $statuses[array_rand($statuses)];
    }

    private function getRandomOrderType()
    {
        $types = ['regular', 'rush', 'bulk', 'wholesale'];
        return $types[array_rand($types)];
    }

    private function getRandomPaymentStatus()
    {
        $statuses = ['pending', 'paid', 'failed', 'refunded'];
        return $statuses[array_rand($statuses)];
    }

    private function getRandomPaymentMethod()
    {
        $methods = ['cash', 'card', 'mobile_money', 'bank_transfer'];
        return $methods[array_rand($methods)];
    }

    private function getRandomInventoryStatus()
    {
        $statuses = ['available', 'reserved', 'damaged', 'expired', 'low_stock'];
        return $statuses[array_rand($statuses)];
    }

    private function getRandomStorageLocation()
    {
        $locations = ['Cold Room A', 'Cold Room B', 'Freezer 1', 'Freezer 2', 'Storage Area 1'];
        return $locations[array_rand($locations)];
    }

    private function getRandomCheckType()
    {
        $types = ['production', 'quality', 'safety', 'compliance'];
        return $types[array_rand($types)];
    }

    private function getRandomPassFailStatus()
    {
        $statuses = ['pass', 'fail', 'conditional'];
        return $statuses[array_rand($statuses)];
    }

    private function getRandomDeliveryStatus()
    {
        $statuses = ['scheduled', 'in_transit', 'delivered', 'failed', 'returned'];
        return $statuses[array_rand($statuses)];
    }

    private function getRandomDeliveryMethod()
    {
        $methods = ['own_vehicle', 'third_party', 'express', 'standard'];
        return $methods[array_rand($methods)];
    }
} 