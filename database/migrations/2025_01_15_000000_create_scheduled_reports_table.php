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
        Schema::create('scheduled_reports', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('report_type');
            $table->json('report_config'); // Store report configuration
            $table->string('frequency'); // daily, weekly, monthly, quarterly, yearly
            $table->string('day_of_week')->nullable(); // For weekly reports
            $table->integer('day_of_month')->nullable(); // For monthly reports
            $table->time('time')->default('09:00:00'); // Time to generate report
            $table->string('timezone')->default('UTC');
            $table->json('recipients'); // Array of email addresses
            $table->string('format')->default('pdf'); // pdf, excel, csv
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_generated_at')->nullable();
            $table->timestamp('next_generation_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('stakeholder_type')->nullable();
            $table->unsignedBigInteger('stakeholder_id')->nullable();
            $table->timestamps();
            
            $table->index(['is_active', 'next_generation_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_reports');
    }
}; 