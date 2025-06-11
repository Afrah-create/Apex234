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
    Schema::create('raw_materials', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('code')->unique();
        $table->text('description');
        $table->string('unit_of_measure'); // kg, liters, pieces
        $table->decimal('unit_price', 10, 2);
        $table->integer('minimum_stock_level');
        $table->integer('reorder_point');
        $table->boolean('perishable')->default(false);
        $table->integer('shelf_life_days')->nullable();
        $table->timestamps();
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
