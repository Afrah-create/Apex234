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
            // You may want to add the foreign key constraint back if needed
            // $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('set null');
        });
    }
}; 