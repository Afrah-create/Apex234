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
        Schema::table('yogurt_products', function (Blueprint $table) {
            $table->dateTime('stock_input_date')->nullable()->after('stock');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('yogurt_products', function (Blueprint $table) {
            $table->dropColumn('stock_input_date');
        });
    }
};
