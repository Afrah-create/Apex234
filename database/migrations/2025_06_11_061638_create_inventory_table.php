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
    Schema::create('inventory', function (Blueprint $table) {
        $table->id();
        $table->string('item_type'); // raw_material or product
        $table->unsignedBigInteger('item_id'); // ID of raw material or product
        $table->string('batch_number');
        $table->integer('quantity');
        $table->decimal('unit_cost', 10, 2);
        $table->date('expiry_date')->nullable();
        $table->date('received_date');
        $table->foreignId('supplier_id')->nullable()->constrained();
        $table->string('location')->nullable();
        $table->enum('status', ['available', 'reserved', 'expired', 'damaged']);
        $table->timestamps();
        
        $table->index(['item_type', 'item_id']);
    });
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};
