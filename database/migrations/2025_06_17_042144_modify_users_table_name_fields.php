<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyUsersTableNameFields extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Make first_name and last_name nullable
            $table->string('first_name')->nullable()->change();
            $table->string('last_name')->nullable()->change();
            
            // OR set default values
            // $table->string('first_name')->default('')->change();
            // $table->string('last_name')->default('')->change();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable(false)->change();
            $table->string('last_name')->nullable(false)->change();
        });
    }
}