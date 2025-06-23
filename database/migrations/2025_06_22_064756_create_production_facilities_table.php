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
        Schema::create('production_facilities', function (Blueprint $table) {
            $table->id();
            $table->string('facility_name');
            $table->string('facility_code')->unique();
            $table->text('facility_address');
            $table->string('facility_phone');
            $table->string('facility_email');
            $table->string('facility_manager');
            $table->string('manager_phone');
            $table->string('manager_email');
            $table->integer('production_capacity'); // daily capacity in liters
            $table->integer('current_production'); // current daily production
            $table->enum('facility_type', ['primary', 'secondary', 'packaging', 'storage']);
            $table->json('equipment')->nullable(); // list of production equipment
            $table->json('certifications')->nullable(); // ISO, HACCP, FDA, etc.
            $table->enum('certification_status', ['certified', 'pending', 'expired', 'suspended'])->default('pending');
            $table->date('last_inspection_date')->nullable();
            $table->date('next_inspection_date')->nullable();
            $table->decimal('temperature_control', 5, 2)->nullable(); // facility temperature
            $table->decimal('humidity_control', 5, 2)->nullable(); // facility humidity
            $table->enum('status', ['operational', 'maintenance', 'shutdown', 'suspended'])->default('operational');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Indexes for better performance
            $table->index('facility_code');
            $table->index('facility_type');
            $table->index('certification_status');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_facilities');
    }
};
