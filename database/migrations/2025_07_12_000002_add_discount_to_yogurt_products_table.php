<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('yogurt_products', function (Blueprint $table) {
            if (!Schema::hasColumn('yogurt_products', 'discount')) {
                $table->decimal('discount', 5, 2)->nullable()->after('selling_price');
            }
            if (!Schema::hasColumn('yogurt_products', 'stock')) {
                $table->integer('stock')->default(0)->after('status');
            }
            if (!Schema::hasColumn('yogurt_products', 'image_path')) {
                $table->string('image_path')->nullable()->after('stock');
            }
        });
    }

    public function down(): void
    {
        Schema::table('yogurt_products', function (Blueprint $table) {
            if (Schema::hasColumn('yogurt_products', 'discount')) {
                $table->dropColumn('discount');
            }
            if (Schema::hasColumn('yogurt_products', 'stock')) {
                $table->dropColumn('stock');
            }
            if (Schema::hasColumn('yogurt_products', 'image_path')) {
                $table->dropColumn('image_path');
            }
        });
    }
}; 