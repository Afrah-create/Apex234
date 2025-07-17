<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->string('license')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();

            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('drivers');
    }
}; 