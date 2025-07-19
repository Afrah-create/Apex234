<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->date('license_expiry')->nullable();
            $table->string('vehicle_number')->nullable();
            $table->string('photo')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropColumn(['license_expiry', 'vehicle_number', 'photo']);
        });
    }
}; 