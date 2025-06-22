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
        Schema::create('distribution_centers', function (Blueprint $table) {
            $table->id();
            $table->string('center_name');
            $table->string('center_code')->unique();
            $table->text('center_address');
            $table->string('center_phone');
            $table->string('center_email');
            $table->string('center_manager');
            $table->string('manager_phone');
            $table->string('manager_email');
            $table->enum('center_type', ['primary', 'secondary', 'regional', 'local']);
            $table->integer('storage_capacity'); // in cubic meters
            $table->integer('current_inventory'); // current storage usage
            $table->decimal('temperature_control', 5, 2); // storage temperature in Celsius
            $table->decimal('humidity_control', 5, 2)->nullable(); // storage humidity
            $table->integer('delivery_vehicles'); // number of delivery vehicles
            $table->integer('delivery_radius'); // delivery radius in km
            $table->json('facilities')->nullable(); // cold storage, loading docks, etc.
            $table->json('certifications')->nullable(); // food safety certifications
            $table->enum('certification_status', ['certified', 'pending', 'expired', 'suspended'])->default('pending');
            $table->date('last_inspection_date')->nullable();
            $table->date('next_inspection_date')->nullable();
            $table->enum('status', ['operational', 'maintenance', 'shutdown', 'suspended'])->default('operational');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Indexes for better performance
            $table->index('center_code');
            $table->index('center_type');
            $table->index('certification_status');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distribution_centers');
    }
};
