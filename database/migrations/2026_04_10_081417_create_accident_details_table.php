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
        Schema::create('accident_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->nullable()->constrained('patients')->nullOnDelete();
            $table->date('date_of_accident')->nullable();
            $table->text('place_of_accident')->nullable();
            $table->dateTime('time_of_accident')->nullable();
            $table->string('veichle_no')->nullable();
            $table->string('veichle_type')->nullable();
            $table->string('veichle_model')->nullable();
            $table->text('accident_description')->nullable();
            $table->text('injury_description')->nullable();
            $table->string('insurance_names')->nullable();
            $table->string('insurance_policy_no')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accident_details');
    }
};
