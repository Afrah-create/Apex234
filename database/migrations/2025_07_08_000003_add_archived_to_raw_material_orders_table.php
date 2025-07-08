<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('raw_material_orders', function (Blueprint $table) {
            $table->boolean('archived')->default(false)->after('status');
        });
    }

    public function down()
    {
        Schema::table('raw_material_orders', function (Blueprint $table) {
            $table->dropColumn('archived');
        });
    }
}; 