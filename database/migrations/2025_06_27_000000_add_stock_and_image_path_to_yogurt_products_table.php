<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('yogurt_products', 'stock')) {
            Schema::table('yogurt_products', function (Blueprint $table) {
                $table->integer('stock')->default(0)->after('status');
            });
        }
        if (!Schema::hasColumn('yogurt_products', 'image_path')) {
            Schema::table('yogurt_products', function (Blueprint $table) {
                $table->string('image_path')->nullable()->after('stock');
            });
        }
    }

    public function down(): void
    {
        Schema::table('yogurt_products', function (Blueprint $table) {
            $table->dropColumn(['stock', 'image_path']);
        });
    }
}; 