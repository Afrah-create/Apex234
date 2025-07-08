<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            // Only add pdf_path if it doesn't already exist
            if (!Schema::hasColumn('deliveries', 'pdf_path')) {
                $table->string('pdf_path')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            if (Schema::hasColumn('deliveries', 'pdf_path')) {
                $table->dropColumn('pdf_path');
            }
        });
    }
}; 