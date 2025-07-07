<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fix material_type ENUM values
        DB::statement("ALTER TABLE raw_materials MODIFY COLUMN material_type ENUM('milk', 'sugar', 'fruit') NOT NULL");
        
        // Fix status ENUM values
        DB::statement("ALTER TABLE raw_materials MODIFY COLUMN status ENUM('available', 'in_use', 'expired', 'disposed') NOT NULL DEFAULT 'available'");
        
        // Fix quality_grade ENUM values
        DB::statement("ALTER TABLE raw_materials MODIFY COLUMN quality_grade ENUM('A', 'B', 'C', 'D') NOT NULL DEFAULT 'A'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original ENUM values if needed
        DB::statement("ALTER TABLE raw_materials MODIFY COLUMN material_type ENUM('milk', 'sugar', 'fruit') NOT NULL");
        DB::statement("ALTER TABLE raw_materials MODIFY COLUMN status ENUM('available', 'in_use', 'expired', 'disposed') NOT NULL DEFAULT 'available'");
        DB::statement("ALTER TABLE raw_materials MODIFY COLUMN quality_grade ENUM('A', 'B', 'C', 'D') NOT NULL DEFAULT 'A'");
    }
};
