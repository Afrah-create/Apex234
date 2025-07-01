<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\User;
use App\Models\Role;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a user for the employee
        $user = User::create([
            'name' => 'Test Employee',
            'email' => 'employee@example.com',
            'password' => bcrypt('password'),
        ]);
        // Assign the 'employee' role
        $employeeRole = Role::where('name', 'employee')->first();
        if ($employeeRole) {
            $user->roles()->syncWithoutDetaching([$employeeRole->id]);
        }
        // Create the employee record linked to the user
        Employee::create([
            'name' => 'Test Employee',
            'role' => 'Production Worker',
            'status' => 'Active',
            'user_id' => $user->id,
        ]);

        Employee::create(['name' => 'Alice', 'role' => 'Production Worker']);
        Employee::create(['name' => 'Bob', 'role' => 'Warehouse Staff']);
        Employee::create(['name' => 'Carol', 'role' => 'Driver']);
        Employee::create(['name' => 'David', 'role' => 'Sales Manager']);
        Employee::create(['name' => 'Eve', 'role' => 'Production Worker']);
        Employee::create(['name' => 'Frank', 'role' => 'Warehouse Staff']);
        Employee::create(['name' => 'Grace', 'role' => 'Driver']);
        Employee::create(['name' => 'Heidi', 'role' => 'Sales Manager']);
    }
}
