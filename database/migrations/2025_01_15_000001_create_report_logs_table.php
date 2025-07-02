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
        Schema::create('report_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scheduled_report_id')->nullable()->constrained('scheduled_reports')->onDelete('cascade');
            $table->string('report_type');
            $table->json('report_config');
            $table->string('format');
            $table->string('status'); // pending, generating, completed, failed, delivered
            $table->text('file_path')->nullable(); // Path to generated file
            $table->json('recipients');
            $table->json('delivery_status')->nullable(); // Track delivery to each recipient
            $table->text('error_message')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->foreignId('generated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['status', 'generated_at']);
            $table->index(['scheduled_report_id', 'generated_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_logs');
    }
}; 