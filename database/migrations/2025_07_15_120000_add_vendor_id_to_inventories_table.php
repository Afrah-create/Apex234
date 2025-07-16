<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('inventories', 'vendor_id')) {
            Schema::table('inventories', function (Blueprint $table) {
                $table->unsignedBigInteger('vendor_id')->nullable()->after('id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            // Uncomment the next line if you added the foreign key
            // $table->dropForeign(['vendor_id']);
            $table->dropColumn('vendor_id');
        });
    }
}; 