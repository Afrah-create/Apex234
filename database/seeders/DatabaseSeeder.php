<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {


        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $vendorRole = Role::firstOrCreate(['name' => 'vendor']);
        $retailerRole = Role::firstOrCreate(['name' => 'retailer']);
        $supplierRole = Role::firstOrCreate(['name' => 'supplier']);
        $employeeRole = Role::firstOrCreate(['name' => 'employee']);

        // Create permissions
        $manageUsers = Permission::firstOrCreate(['name' => 'manage users']);
        $manageOrders = Permission::firstOrCreate(['name' => 'manage orders']);
        $viewReports = Permission::firstOrCreate(['name' => 'view reports']);

        // Assign permissions to roles
        $adminRole->permissions()->sync([$manageUsers->id, $manageOrders->id, $viewReports->id]);
        $vendorRole->permissions()->sync([$manageOrders->id]);
        $retailerRole->permissions()->sync([]);
        $supplierRole->permissions()->sync([]);

        // Assign admin role to the first user (if exists)
        $user = User::first();
        if ($user) {
            $user->roles()->sync([$adminRole->id]);
        }

        // Create sample users
        $admin = User::firstOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Admin User',
            'password' => bcrypt('password'),
            'chat_background' => '',
        ]);
        $vendor = User::firstOrCreate([
            'email' => 'vendor@example.com',
        ], [
            'name' => 'Vendor User',
            'password' => bcrypt('password'),
            'chat_background' => '',
        ]);

        // Ensure vendor@example.com has a Vendor profile
        \App\Models\Vendor::firstOrCreate([
            'user_id' => $vendor->id,
        ], [
            'company_name' => 'Default Vendor Co.',
            'business_name' => 'Default Vendor Co.',
            'registration_number' => 'VEN-001',
            'business_address' => '456 Vendor Street',
            'contact_person' => 'Vendor Contact',
            'contact_phone' => '+1234567899',
            'phone_number' => '+1234567899',
            'contact_email' => 'vendor@example.com',
        ]);
        $retailer = User::firstOrCreate([
            'email' => 'retailer@example.com',
        ], [
            'name' => 'Retailer User',
            'password' => bcrypt('password'),
            'chat_background' => '',
        ]);
        $supplier = User::firstOrCreate([
            'email' => 'supplier@example.com',
        ], [
            'name' => 'Supplier User',
            'password' => bcrypt('password'),
            'chat_background' => '',
        ]);

        // Ensure supplier@example.com has a Supplier profile
        \App\Models\Supplier::firstOrCreate([
            'user_id' => $supplier->id,
        ], [
            'company_name' => 'Default Supplier Co.',
            'registration_number' => 'SUP-001',
            'business_address' => '123 Supplier Lane',
            'contact_person' => 'Supplier Contact',
            'contact_phone' => '+1234567890',
            'contact_email' => 'supplier@example.com',
        ]);

        // Assign roles to users
        $admin->roles()->syncWithoutDetaching([$adminRole->id]);
        $vendor->roles()->syncWithoutDetaching([$vendorRole->id]);
        $retailer->roles()->syncWithoutDetaching([$retailerRole->id]);
        $supplier->roles()->syncWithoutDetaching([$supplierRole->id]);

        // Optionally, assign direct permissions to a user (example)
        $vendor->roles()->syncWithoutDetaching([$vendorRole->id]);
        // $vendor->permissions()->attach($manageOrders->id); // Uncomment if you want direct user-permission

        // Seed users
        $users = \App\Models\User::factory(2)->create();

        // Seed production facilities
        $productionFacilities = \App\Models\ProductionFacility::factory(2)->create();

        // Seed distribution centers
        $distributionCenters = \App\Models\DistributionCenter::factory(2)->create();

        // Seed yogurt products using the new seeder with UGX pricing
        $this->call([
            YogurtProductSeeder::class,
            DairyFarmSeeder::class,
            ScheduledReportsSeeder::class
        ]);
        $yogurtProducts = \App\Models\YogurtProduct::all(); // Get the seeded products

        // Seed retailers (each linked to a user)
        $retailers = \App\Models\Retailer::factory(2)->create([
            'user_id' => $users->random()->id,
        ]);

        // Seed suppliers (each linked to a user)
        $suppliers = \App\Models\Supplier::factory(2)->create([
            'user_id' => $users->random()->id,
        ]);

        // Seed vendors (each linked to a user)
        $vendorUsers = \App\Models\User::factory(5)->create(['role' => 'vendor']);
        foreach ($vendorUsers as $vendorUser) {
            \App\Models\Vendor::factory()->create([
                'user_id' => $vendorUser->id,
            ]);
        }

        // Assign vendor role to vendor users
        foreach ($vendorUsers as $vendorUser) {
            $vendorUser->roles()->syncWithoutDetaching([$vendorRole->id]);
        }

        // Seed orders (each linked to a retailer and distribution center)
        $orders = \App\Models\Order::factory(2)->create([
            'retailer_id' => $retailers->random()->id,
            'distribution_center_id' => $distributionCenters->random()->id,
        ]);

        // Seed inventory: ensure the three main products are present in every distribution center
        foreach ($distributionCenters as $center) {
            foreach ($yogurtProducts as $product) {
                \App\Models\Inventory::factory()->create([
                    'yogurt_product_id' => $product->id,
                    'distribution_center_id' => $center->id,
                ]);
            }
        }

        // (Optional) Seed additional random inventory for vendor-added products
        // $inventory = \App\Models\Inventory::factory(30)->create([
        //     'yogurt_product_id' => $yogurtProducts->random()->id,
        //     'distribution_center_id' => $distributionCenters->random()->id,
        // ]);

        // Seed quality checks (each linked to a yogurt product and production facility)
        $qualityChecks = \App\Models\QualityCheck::factory(2)->create([
            'yogurt_product_id' => $yogurtProducts->random()->id,
            'production_facility_id' => $productionFacilities->random()->id,
        ]);

        // Seed deliveries (each linked to an order, distribution center, and retailer)
        $deliveries = \App\Models\Delivery::factory(2)->create([
            'order_id' => $orders->random()->id,
            'distribution_center_id' => $distributionCenters->random()->id,
            'retailer_id' => $retailers->random()->id,
        ]);

        // Seed drivers for each supplier
        \App\Models\Supplier::all()->each(function($supplier) {
            if ($supplier->drivers()->count() < 3) {
                $drivers = [
                    ['name' => 'John Doe', 'phone' => '+1234567891', 'license' => 'DL-12345'],
                    ['name' => 'Jane Smith', 'phone' => '+1234567892', 'license' => 'DL-67890'],
                    ['name' => 'Mike Brown', 'phone' => '+1234567893', 'license' => 'DL-54321'],
                ];
                foreach ($drivers as $driver) {
                    $supplier->drivers()->firstOrCreate(['license' => $driver['license']], $driver);
                }
            }
        });

        // Seed employees
        $this->call(EmployeeSeeder::class);

        // Ensure the 'employee' role exists
        \App\Models\Role::firstOrCreate(['name' => 'employee']);
    }
}
