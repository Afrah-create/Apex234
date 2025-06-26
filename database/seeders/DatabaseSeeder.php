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
        // User::factory(10)->create();

    //    User::factory()->create([
      //      'name' => 'Test User',
       //     'email' => 'test@example.com',
       // ]);

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
        $users = \App\Models\User::factory(30)->create();

        // Seed production facilities
        $productionFacilities = \App\Models\ProductionFacility::factory(30)->create();

        // Seed distribution centers
        $distributionCenters = \App\Models\DistributionCenter::factory(30)->create();

        // Seed yogurt products (each linked to a production facility)
        // $yogurtProducts = \App\Models\YogurtProduct::factory(30)->create([
        //     'production_facility_id' => $productionFacilities->random()->id,
        // ]);

        // Manually seed the three yogurt products
        $firstFacility = $productionFacilities->first();

        $yogurtProductsData = collect([
            [
                'production_facility_id' => $firstFacility->id,
                'product_name' => 'Greek Vanilla Yoghurt',
                'product_code' => 'YOG-GREEK-VAN',
                'description' => 'Thick, creamy Greek yoghurt with natural vanilla flavor.',
                'product_type' => 'greek',
                'flavor' => 'vanilla',
                'fat_content' => 8.0,
                'protein_content' => 6.0,
                'sugar_content' => 5.0,
                'calories_per_100g' => 120,
                'package_size' => '150g',
                'package_type' => 'cup',
                'shelf_life_days' => 21,
                'storage_temperature' => 4.0,
                'ingredients' => json_encode(['milk', 'vanilla', 'yogurt cultures']),
                'nutritional_info' => null,
                'allergens' => json_encode(['milk']),
                'production_cost' => 0.80,
                'selling_price' => 1.50,
                'status' => 'active',
                'notes' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'production_facility_id' => $firstFacility->id,
                'product_name' => 'Low Fat Blueberry Yoghurt',
                'product_code' => 'YOG-LOWFAT-BLUE',
                'description' => 'Low fat yoghurt with real blueberry pieces.',
                'product_type' => 'low_fat',
                'flavor' => 'blueberry',
                'fat_content' => 2.0,
                'protein_content' => 4.0,
                'sugar_content' => 7.0,
                'calories_per_100g' => 90,
                'package_size' => '150g',
                'package_type' => 'cup',
                'shelf_life_days' => 18,
                'storage_temperature' => 4.0,
                'ingredients' => json_encode(['milk', 'blueberry', 'yogurt cultures']),
                'nutritional_info' => null,
                'allergens' => json_encode(['milk']),
                'production_cost' => 0.70,
                'selling_price' => 1.40,
                'status' => 'active',
                'notes' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'production_facility_id' => $firstFacility->id,
                'product_name' => 'Organic Strawberry Yoghurt',
                'product_code' => 'YOG-ORG-STRAW',
                'description' => 'Organic yoghurt made with fresh strawberries.',
                'product_type' => 'organic',
                'flavor' => 'strawberry',
                'fat_content' => 4.0,
                'protein_content' => 5.0,
                'sugar_content' => 6.0,
                'calories_per_100g' => 100,
                'package_size' => '150g',
                'package_type' => 'cup',
                'shelf_life_days' => 16,
                'storage_temperature' => 4.0,
                'ingredients' => json_encode(['organic milk', 'strawberry', 'yogurt cultures']),
                'nutritional_info' => null,
                'allergens' => json_encode(['milk']),
                'production_cost' => 0.90,
                'selling_price' => 1.60,
                'status' => 'active',
                'notes' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
        foreach ($yogurtProductsData as $product) {
            \App\Models\YogurtProduct::create($product);
        }
        $yogurtProducts = \App\Models\YogurtProduct::all(); // Now this is a collection of models

        // Seed retailers (each linked to a user)
        $retailers = \App\Models\Retailer::factory(30)->create([
            'user_id' => $users->random()->id,
        ]);

        // Seed suppliers (each linked to a user)
        $suppliers = \App\Models\Supplier::factory(30)->create([
            'user_id' => $users->random()->id,
        ]);

        // Seed vendors (no foreign keys)
        $vendors = \App\Models\Vendor::factory(30)->create();

        // Seed orders (each linked to a retailer and distribution center)
        $orders = \App\Models\Order::factory(30)->create([
            'retailer_id' => $retailers->random()->id,
            'distribution_center_id' => $distributionCenters->random()->id,
        ]);

        // Seed inventory (each linked to a yogurt product and distribution center)
        $inventory = \App\Models\Inventory::factory(30)->create([
            'yogurt_product_id' => $yogurtProducts->random()->id,
            'distribution_center_id' => $distributionCenters->random()->id,
        ]);

        // Seed quality checks (each linked to a yogurt product and production facility)
        $qualityChecks = \App\Models\QualityCheck::factory(30)->create([
            'yogurt_product_id' => $yogurtProducts->random()->id,
            'production_facility_id' => $productionFacilities->random()->id,
        ]);

        // Seed deliveries (each linked to an order, distribution center, and retailer)
        $deliveries = \App\Models\Delivery::factory(30)->create([
            'order_id' => $orders->random()->id,
            'distribution_center_id' => $distributionCenters->random()->id,
            'retailer_id' => $retailers->random()->id,
        ]);
    }
}
