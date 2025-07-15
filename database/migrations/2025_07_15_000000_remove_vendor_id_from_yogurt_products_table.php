<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('yogurt_products', function (Blueprint $table) {
            if (Schema::hasColumn('yogurt_products', 'vendor_id')) {
                // Drop the foreign key constraint if it exists
                try {
                    $table->dropForeign(['vendor_id']);
                } catch (\Exception $e) {
                    // Ignore if the foreign key does not exist
                }
                $table->dropColumn('vendor_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('yogurt_products', function (Blueprint $table) {
            $table->unsignedBigInteger('vendor_id')->nullable();
            // Optionally add the foreign key constraint back
            // $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('set null');
        });
    }
}; 