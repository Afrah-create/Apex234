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
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('yogurt_product_id')->constrained()->onDelete('cascade');
            $table->foreignId('distribution_center_id')->constrained()->onDelete('cascade');
            $table->foreignId('quality_check_id')->nullable()->constrained()->onDelete('set null');
            $table->string('batch_number');
            $table->integer('quantity_available');
            $table->integer('quantity_reserved');
            $table->integer('quantity_damaged')->default(0);
            $table->integer('quantity_expired')->default(0);
            $table->date('production_date');
            $table->date('expiry_date');
            $table->decimal('storage_temperature', 5, 2); // in Celsius
            $table->enum('storage_location', ['cold_room', 'refrigerator', 'freezer', 'warehouse']);
            $table->string('shelf_location')->nullable(); // specific shelf/rack location
            $table->enum('inventory_status', ['available', 'low_stock', 'out_of_stock', 'expired', 'damaged'])->default('available');
            $table->decimal('unit_cost', 10, 2);
            $table->decimal('total_value', 12, 2);
            $table->date('last_updated');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['yogurt_product_id', 'distribution_center_id']);
            $table->index('batch_number');
            $table->index('expiry_date');
            $table->index('inventory_status');
            $table->index('storage_location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};
