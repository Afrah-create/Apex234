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
        Schema::create('yogurt_products', function (Blueprint $table) {
            $table->id();
            $table->string('product_name');
            $table->string('product_type');
            $table->string('product_code')->unique();
            $table->decimal('selling_price', 10, 2);
            $table->integer('stock')->default(0);
            $table->string('status')->default('active');
            $table->unsignedBigInteger('production_facility_id')->nullable();
            $table->string('image_path')->nullable();
            $table->timestamps();
            // vendor_id column removed as per new inventory model
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('yogurt_products');
    }
};
