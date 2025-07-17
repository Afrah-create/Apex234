<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('yogurt_products', function (Blueprint $table) {
            if (!Schema::hasColumn('yogurt_products', 'flavor')) {
                $table->string('flavor')->nullable()->after('product_type');
            }
            if (!Schema::hasColumn('yogurt_products', 'fat_content')) {
                $table->float('fat_content')->nullable()->after('flavor');
            }
            if (!Schema::hasColumn('yogurt_products', 'protein_content')) {
                $table->float('protein_content')->nullable()->after('fat_content');
            }
            if (!Schema::hasColumn('yogurt_products', 'sugar_content')) {
                $table->float('sugar_content')->nullable()->after('protein_content');
            }
            if (!Schema::hasColumn('yogurt_products', 'calories_per_100g')) {
                $table->integer('calories_per_100g')->nullable()->after('sugar_content');
            }
            if (!Schema::hasColumn('yogurt_products', 'package_size')) {
                $table->string('package_size')->nullable()->after('calories_per_100g');
            }
            if (!Schema::hasColumn('yogurt_products', 'package_type')) {
                $table->string('package_type')->nullable()->after('package_size');
            }
            if (!Schema::hasColumn('yogurt_products', 'shelf_life_days')) {
                $table->integer('shelf_life_days')->nullable()->after('package_type');
            }
            if (!Schema::hasColumn('yogurt_products', 'storage_temperature')) {
                $table->string('storage_temperature')->nullable()->after('shelf_life_days');
            }
            if (!Schema::hasColumn('yogurt_products', 'ingredients')) {
                $table->json('ingredients')->nullable()->after('storage_temperature');
            }
            if (!Schema::hasColumn('yogurt_products', 'nutritional_info')) {
                $table->json('nutritional_info')->nullable()->after('ingredients');
            }
            if (!Schema::hasColumn('yogurt_products', 'allergens')) {
                $table->json('allergens')->nullable()->after('nutritional_info');
            }
            if (!Schema::hasColumn('yogurt_products', 'production_cost')) {
                $table->decimal('production_cost', 10, 2)->nullable()->after('allergens');
            }
            if (!Schema::hasColumn('yogurt_products', 'notes')) {
                $table->text('notes')->nullable()->after('status');
            }
        });
    }

    public function down()
    {
        Schema::table('yogurt_products', function (Blueprint $table) {
            $columns = [
                'flavor', 'fat_content', 'protein_content', 'sugar_content', 'calories_per_100g',
                'package_size', 'package_type', 'shelf_life_days', 'storage_temperature',
                'ingredients', 'nutritional_info', 'allergens', 'production_cost', 'notes'
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('yogurt_products', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}; 