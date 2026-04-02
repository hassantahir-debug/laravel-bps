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
            $table->enum('appointment_type', ['Initial', 'Follow-up', 'Consultation', 'Procedure', 'Emergency', 'Telehealth', 'Routine Checkup'])->default('Initial');
            $table->enum('appointment_status', ['Scheduled', 'Confirmed', 'Checked In', 'In Progress', 'Completed', 'Cancelled', 'No Show', 'Rescheduled'])->default('Scheduled');
            $table->date('appointment_date');
            $table->time('appointment_time');
            $table->integer('duration_minutes')->default(30);
            $table->foreignId('doctor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('doctor_name')->nullable();
            $table->string('specialty_required')->nullable();
            $table->text('notes')->nullable();
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
