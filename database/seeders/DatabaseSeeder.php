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
        ]);
        $vendor = User::firstOrCreate([
            'email' => 'vendor@example.com',
        ], [
            'name' => 'Vendor User',
            'password' => bcrypt('password'),
        ]);
        $retailer = User::firstOrCreate([
            'email' => 'retailer@example.com',
        ], [
            'name' => 'Retailer User',
            'password' => bcrypt('password'),
        ]);
        $supplier = User::firstOrCreate([
            'email' => 'supplier@example.com',
        ], [
            'name' => 'Supplier User',
            'password' => bcrypt('password'),
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

        // Seed vendors (no foreign keys)
        $vendors = \App\Models\Vendor::factory(30)->create();

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

        // Seed employees
        $this->call(EmployeeSeeder::class);
    }
}
