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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('firstName');
            $table->string('middleName')->nullable();
            $table->string('lastName');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('mobile');
            $table->date('dateOfBirth');
            $table->enum('gender',['male','female','other']);
            $table->string('address');
            $table->string('city');
            $table->string('state');
            $table->string('postalCode');
            $table->string('country');
            $table->string('emergencyContactName');
            $table->string('emergencyContactPhone');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
