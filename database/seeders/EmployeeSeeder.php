<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Employee;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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
