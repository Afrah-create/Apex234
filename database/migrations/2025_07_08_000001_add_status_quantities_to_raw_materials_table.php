<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('raw_materials', function (Blueprint $table) {
            $table->integer('available')->nullable()->after('quantity');
            $table->integer('in_use')->nullable()->after('available');
            $table->integer('expired')->nullable()->after('in_use');
            $table->integer('disposed')->nullable()->after('expired');
        });
    }

    public function down()
    {
        Schema::table('raw_materials', function (Blueprint $table) {
            $table->dropColumn(['available', 'in_use', 'expired', 'disposed']);
        });
    }
}; 