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
        Schema::create('procedure_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Standardized code (e.g., CPT)
            $table->text('description')->nullable();     // Detailed description
            $table->timestamps();            // created_at and updated_at
            $table->softDeletes();           // deleted_at for soft deletes
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procedure_codes');
    }
};
