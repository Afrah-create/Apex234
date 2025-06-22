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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('company_name');
        $table->string('registration_number')->unique();
        $table->text('business_address');
        $table->string('contact_person');
        $table->string('contact_phone');
        $table->string('contact_email');
        $table->enum('status', ['pending', 'approved', 'rejected', 'suspended'])->default('pending');
        $table->decimal('rating', 3, 2)->default(0);
        $table->json('certifications')->nullable();
        $table->date('verification_date')->nullable();
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
