<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('retailers', function (Blueprint $table) {
                $table->string('business_name')->nullable();
                $table->string('business_address')->nullable();
                $table->string('contact_person')->nullable();
                $table->string('contact_email')->nullable();
                $table->string('contact_phone')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('retailers', function (Blueprint $table) {
            $table->dropColumn([
                'business_name',
                'business_address',
                'contact_person',
                'contact_email',
                'contact_phone',
            ]);
        });
    }
};
