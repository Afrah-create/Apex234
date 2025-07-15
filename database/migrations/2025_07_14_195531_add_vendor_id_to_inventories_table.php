<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Added this import for DB facade

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->unsignedBigInteger('vendor_id')->nullable()->after('id');
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('set null');
        });

        // Modify the ENUM to add 'customer' to the allowed values
        DB::statement("ALTER TABLE orders MODIFY COLUMN order_type ENUM('regular', 'rush', 'bulk', 'seasonal', 'customer')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropForeign(['vendor_id']);
            $table->dropColumn('vendor_id');
        });

        // Revert to the previous ENUM values (remove 'customer')
        DB::statement("ALTER TABLE orders MODIFY COLUMN order_type ENUM('regular', 'rush', 'bulk', 'seasonal')");
    }
};
