<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // This migration is being modified to remove user_id from employees
        // Employees should only belong to vendors, not directly to users
        // No action needed in up() method
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No action needed in down() method
    }
};
