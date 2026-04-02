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
        Schema::create('cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->string('case_number')->unique();
            $table->enum('case_type', ['New', 'Follow-up', 'Emergency', 'Consultation', 'Surgical', 'Chronic'])->default('New');
            $table->enum('case_category', ['General Medicine', 'Pediatrics', 'Cardiology', 'Orthopedics', 'Dermatology', 'Neurology', 'Gynecology', 'Ophthalmology', 'ENT', 'Dental', 'Psychiatry', 'Other'])->default('General Medicine');
            $table->enum('priority', ['Low', 'Normal', 'High', 'Urgent'])->default('Normal');
            $table->enum('status', ['Active', 'Closed', 'Transferred', 'On Hold'])->default('Active');
            $table->text('description');
            $table->date('opened_date');
            $table->date('closed_date')->nullable();
            $table->foreignId('referring_doctor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cases');
    }
};
