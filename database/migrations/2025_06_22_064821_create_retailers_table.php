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
        Schema::create('retailers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('store_name');
            $table->string('store_code')->unique();
            $table->text('store_address');
            $table->string('store_phone');
            $table->string('store_email');
            $table->string('store_manager');
            $table->string('manager_phone');
            $table->string('manager_email');
            $table->enum('store_type', ['supermarket', 'convenience_store', 'specialty_store', 'online', 'wholesale']);
            $table->enum('store_size', ['small', 'medium', 'large', 'extra_large']);
            $table->integer('daily_customer_traffic')->nullable();
            $table->decimal('monthly_sales_volume', 12, 2)->nullable();
            $table->json('payment_methods')->nullable(); // cash, card, mobile, etc.
            $table->json('store_hours')->nullable(); // operating hours
            $table->enum('certification_status', ['certified', 'pending', 'expired', 'suspended'])->default('pending');
            $table->json('certifications')->nullable(); // food safety, quality certifications
            $table->date('last_inspection_date')->nullable();
            $table->date('next_inspection_date')->nullable();
            $table->decimal('customer_rating', 3, 2)->default(0);
            $table->enum('status', ['active', 'inactive', 'suspended', 'closed'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['user_id', 'status']);
            $table->index('store_code');
            $table->index('store_type');
            $table->index('certification_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retailers');
    }
};
