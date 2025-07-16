<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('vendor_id')->nullable()->after('retailer_id');
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('set null');
            $table->index('vendor_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['vendor_id']);
            $table->dropIndex(['vendor_id']);
            $table->dropColumn('vendor_id');
        });
    }
}; 