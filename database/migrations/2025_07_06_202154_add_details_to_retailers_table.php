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
            // user_id already exists, so we don't need to add it
            // Add only the fields that might be missing or need to be updated
            if (!Schema::hasColumn('retailers', 'business_name')) {
                $table->string('business_name')->nullable();
            }
            if (!Schema::hasColumn('retailers', 'business_address')) {
                $table->string('business_address')->nullable();
            }
            if (!Schema::hasColumn('retailers', 'contact_person')) {
                $table->string('contact_person')->nullable();
            }
            if (!Schema::hasColumn('retailers', 'contact_email')) {
                $table->string('contact_email')->nullable();
            }
            if (!Schema::hasColumn('retailers', 'contact_phone')) {
                $table->string('contact_phone')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('retailers', function (Blueprint $table) {
            $table->dropColumn(['business_name', 'business_address', 'contact_person', 'contact_email', 'contact_phone']);
        });
    }
};
