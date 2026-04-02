<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained('cases')->onDelete('cascade');
            $table->enum('appointment_type', ['Initial', 'Follow-up', 'Emergency', 'Routine Checkup', 'Consultation', 'Procedure', 'Telehealth'])->default('Initial Consultation');
            $table->enum('appointment_status', ['Scheduled', 'In Progress', 'Checked In', 'Completed', 'Cancelled', 'Confirmed', 'No Show', 'Rescheduled'])->default('Scheduled');
            $table->dateTime('appointment_date');
            $table->time('appointment_time');
            $table->text('notes')->nullable();
            $table->string('doctor_name')->nullable();
            $table->foreignId('doctor_id')->constrained('users')->onDelete('set null');
            $table->integer('duration_minutes')->default(30);
            $table->string('specialty_required')->nullable();
            $table->boolean('reminder_sent')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
