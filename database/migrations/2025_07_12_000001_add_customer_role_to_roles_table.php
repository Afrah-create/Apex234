<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        // Only insert if not exists
        if (!DB::table('roles')->where('name', 'customer')->exists()) {
            DB::table('roles')->insert([
                'name' => 'customer',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down()
    {
        DB::table('roles')->where('name', 'customer')->delete();
    }
}; 