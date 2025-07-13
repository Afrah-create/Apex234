<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDistributionCenterVendorTable extends Migration
{
    public function up()
    {
        Schema::create('distribution_center_vendor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('distribution_center_id')->constrained()->onDelete('cascade');
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('distribution_center_vendor');
    }
} 