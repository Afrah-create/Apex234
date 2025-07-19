<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->unsignedBigInteger('retailer_id')->nullable()->change();
            $table->unsignedBigInteger('vendor_id')->nullable()->change();
            $table->string('driver_name')->nullable()->change();
            $table->string('driver_phone')->nullable()->change();
            $table->date('scheduled_delivery_date')->nullable()->change();
            $table->time('scheduled_delivery_time')->nullable()->change();
            $table->string('delivery_number')->nullable()->change();
            if (!Schema::hasColumn('deliveries', 'actual_departure_time')) {
                $table->timestamp('actual_departure_time')->nullable()->after('scheduled_delivery_time');
            }
            if (!Schema::hasColumn('deliveries', 'actual_delivery_time')) {
                $table->timestamp('actual_delivery_time')->nullable()->after('actual_departure_time');
            }
        });
    }

    public function down(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->unsignedBigInteger('retailer_id')->nullable(false)->change();
            $table->unsignedBigInteger('vendor_id')->nullable(false)->change();
            $table->string('driver_name')->nullable(false)->change();
            $table->string('driver_phone')->nullable(false)->change();
            $table->date('scheduled_delivery_date')->nullable(false)->change();
            $table->time('scheduled_delivery_time')->nullable(false)->change();
            $table->string('delivery_number')->nullable(false)->change();
            if (Schema::hasColumn('deliveries', 'actual_departure_time')) {
                $table->dropColumn('actual_departure_time');
            }
            if (Schema::hasColumn('deliveries', 'actual_delivery_time')) {
                $table->dropColumn('actual_delivery_time');
            }
        });
    }
}; 