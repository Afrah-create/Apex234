<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('production_batches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity_produced');
            $table->string('batch_code')->unique();
            $table->timestamps();
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('yogurt_products')->onDelete('cascade');
        });
        Schema::create('production_batch_raw_materials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('production_batch_id');
            $table->unsignedBigInteger('raw_material_id');
            $table->integer('quantity_used');
            $table->timestamps();
            $table->foreign('production_batch_id')->references('id')->on('production_batches')->onDelete('cascade');
            $table->foreign('raw_material_id')->references('id')->on('raw_materials')->onDelete('cascade');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('production_batch_raw_materials');
        Schema::dropIfExists('production_batches');
    }
}; 