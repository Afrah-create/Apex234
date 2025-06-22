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
            $table->foreignId('production_facility_id')->constrained()->onDelete('cascade');
            $table->string('product_name');
            $table->string('product_code')->unique();
            $table->text('description')->nullable();
            $table->enum('product_type', ['plain', 'flavored', 'greek', 'low_fat', 'organic', 'probiotic']);
            $table->string('flavor')->nullable(); // strawberry, vanilla, etc.
            $table->decimal('fat_content', 5, 2); // percentage
            $table->decimal('protein_content', 5, 2); // percentage
            $table->decimal('sugar_content', 5, 2); // percentage
            $table->integer('calories_per_100g');
            $table->string('package_size'); // 150g, 500g, 1L, etc.
            $table->string('package_type'); // cup, bottle, pouch, etc.
            $table->integer('shelf_life_days');
            $table->decimal('storage_temperature', 5, 2); // in Celsius
            $table->json('ingredients')->nullable(); // list of ingredients
            $table->json('nutritional_info')->nullable(); // detailed nutritional information
            $table->json('allergens')->nullable(); // list of allergens
            $table->decimal('production_cost', 10, 2);
            $table->decimal('selling_price', 10, 2);
            $table->enum('status', ['active', 'discontinued', 'seasonal', 'out_of_stock'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['production_facility_id', 'product_type']);
            $table->index('product_code');
            $table->index('product_type');
            $table->index('status');
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
