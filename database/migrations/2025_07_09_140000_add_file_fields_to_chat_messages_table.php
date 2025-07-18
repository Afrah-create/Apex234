<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            // $table->string('file_path')->nullable();
            // $table->string('file_type')->nullable();
            // $table->string('original_name')->nullable();
        });
    }

    public function down()
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropColumn(['file_path', 'file_type', 'original_name']);
        });
    }
}; 