<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('warehouse_staff_id')->nullable()->after('driver_id');
            $table->foreign('warehouse_staff_id')->references('id')->on('employees')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['warehouse_staff_id']);
            $table->dropColumn('warehouse_staff_id');
        });
    }
}; 