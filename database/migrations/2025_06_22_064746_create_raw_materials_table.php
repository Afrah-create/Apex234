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
        Schema::create('raw_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dairy_farm_id')->constrained()->onDelete('cascade');
            $table->string('material_name');
            $table->string('material_code')->unique();
            $table->enum('material_type', ['milk', 'culture', 'flavoring', 'sweetener', 'stabilizer', 'other']);
            $table->text('description')->nullable();
            $table->decimal('quantity', 10, 2); // in appropriate units
            $table->string('unit_of_measure'); // liters, kg, grams, etc.
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_cost', 12, 2);
            $table->date('harvest_date');
            $table->date('expiry_date');
            $table->enum('quality_grade', ['A', 'B', 'C', 'D'])->default('A');
            $table->decimal('temperature', 5, 2)->nullable(); // storage temperature
            $table->decimal('ph_level', 3, 1)->nullable();
            $table->decimal('fat_content', 5, 2)->nullable(); // for milk
            $table->decimal('protein_content', 5, 2)->nullable(); // for milk
            $table->enum('status', ['available', 'in_use', 'expired', 'disposed'])->default('available');
            $table->text('quality_notes')->nullable();
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['dairy_farm_id', 'material_type']);
            $table->index('material_code');
            $table->index('expiry_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raw_materials');
    }
};
