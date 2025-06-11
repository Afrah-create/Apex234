<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('orders', function (Blueprint $table) {
        $table->id();
        $table->string('order_number')->unique();
        $table->foreignId('customer_id')->constrained('users');
        $table->enum('order_type', ['purchase', 'sale', 'transfer']);
        $table->enum('status', ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled']);
        $table->decimal('total_amount', 12, 2);
        $table->decimal('tax_amount', 10, 2)->default(0);
        $table->decimal('shipping_cost', 10, 2)->default(0);
        $table->json('shipping_address');
        $table->date('order_date');
        $table->date('expected_delivery_date')->nullable();
        $table->date('actual_delivery_date')->nullable();
        $table->text('notes')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
