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
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('distribution_center_id')->constrained()->onDelete('cascade');
            $table->foreignId('retailer_id')->constrained()->onDelete('cascade');
            $table->string('delivery_number')->unique();
            $table->string('vehicle_number')->nullable();
            $table->string('driver_name');
            $table->string('driver_phone');
            $table->string('driver_license')->nullable();
            $table->date('scheduled_delivery_date');
            $table->time('scheduled_delivery_time');
            $table->date('actual_delivery_date')->nullable();
            $table->time('actual_delivery_time')->nullable();
            $table->enum('delivery_status', ['scheduled', 'in_transit', 'out_for_delivery', 'delivered', 'failed', 'cancelled'])->default('scheduled');
            $table->decimal('delivery_temperature', 5, 2)->nullable(); // temperature during delivery
            $table->enum('temperature_status', ['maintained', 'fluctuated', 'exceeded_limit'])->nullable();
            $table->integer('delivery_duration')->nullable(); // in minutes
            $table->decimal('delivery_distance', 8, 2)->nullable(); // in kilometers
            $table->decimal('fuel_consumption', 6, 2)->nullable(); // in liters
            $table->text('delivery_address');
            $table->string('recipient_name');
            $table->string('recipient_phone');
            $table->string('recipient_signature')->nullable();
            $table->text('delivery_notes')->nullable();
            $table->enum('customer_satisfaction', ['excellent', 'good', 'fair', 'poor'])->nullable();
            $table->text('customer_feedback')->nullable();
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['order_id', 'delivery_status']);
            $table->index('delivery_number');
            $table->index('scheduled_delivery_date');
            $table->index('delivery_status');
            $table->index('driver_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
