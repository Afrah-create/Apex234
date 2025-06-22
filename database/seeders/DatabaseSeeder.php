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

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

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
    }
}
