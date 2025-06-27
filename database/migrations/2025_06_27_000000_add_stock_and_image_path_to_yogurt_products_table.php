<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('yogurt_products', function (Blueprint $table) {
            $table->integer('stock')->default(0)->after('status');
            $table->string('image_path')->nullable()->after('stock');
        });
    }

    public function down(): void
    {
        Schema::table('yogurt_products', function (Blueprint $table) {
            $table->dropColumn(['stock', 'image_path']);
        });
    }
}; 