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
        Schema::create('quality_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('yogurt_product_id')->constrained()->onDelete('cascade');
            $table->foreignId('production_facility_id')->constrained()->onDelete('cascade');
            $table->string('batch_number')->unique();
            $table->date('production_date');
            $table->date('expiry_date');
            $table->decimal('ph_level', 3, 1); // should be 4.0-4.6 for yogurt
            $table->decimal('temperature', 5, 2); // in Celsius
            $table->decimal('fat_content', 5, 2); // percentage
            $table->decimal('protein_content', 5, 2); // percentage
            $table->decimal('moisture_content', 5, 2)->nullable(); // percentage
            $table->decimal('total_solids', 5, 2)->nullable(); // percentage
            $table->integer('bacteria_count')->nullable(); // CFU/g
            $table->integer('yeast_count')->nullable(); // CFU/g
            $table->integer('mold_count')->nullable(); // CFU/g
            $table->enum('consistency', ['smooth', 'lumpy', 'watery', 'thick'])->default('smooth');
            $table->enum('color', ['white', 'off_white', 'yellowish', 'other'])->default('white');
            $table->enum('taste', ['good', 'acceptable', 'poor', 'unacceptable'])->default('good');
            $table->enum('odor', ['normal', 'slight_off', 'strong_off', 'unacceptable'])->default('normal');
            $table->enum('overall_quality', ['excellent', 'good', 'acceptable', 'poor', 'rejected'])->default('good');
            $table->string('inspector_name');
            $table->text('notes')->nullable();
            $table->json('test_results')->nullable(); // additional test results
            $table->enum('status', ['pending', 'passed', 'failed', 'retest'])->default('pending');
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['yogurt_product_id', 'production_date']);
            $table->index('batch_number');
            $table->index('production_date');
            $table->index('overall_quality');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quality_checks');
    }
};
