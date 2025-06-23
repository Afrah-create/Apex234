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
        Schema::create('dairy_farms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->string('farm_name');
            $table->string('farm_code')->unique();
            $table->text('farm_address');
            $table->string('farm_phone');
            $table->string('farm_email');
            $table->string('farm_manager');
            $table->string('manager_phone');
            $table->string('manager_email');
            $table->integer('total_cattle');
            $table->integer('milking_cattle');
            $table->decimal('daily_milk_production', 8, 2); // in liters
            $table->enum('certification_status', ['certified', 'pending', 'expired', 'suspended'])->default('pending');
            $table->json('certifications')->nullable(); // ISO, HACCP, etc.
            $table->date('last_inspection_date')->nullable();
            $table->date('next_inspection_date')->nullable();
            $table->decimal('quality_rating', 3, 2)->default(0);
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['supplier_id', 'status']);
            $table->index('farm_code');
            $table->index('certification_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dairy_farms');
    }
};
