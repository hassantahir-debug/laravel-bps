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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_id')->constrained('bills')->onDelete('cascade');
            $table->enum('document_type', ['ChequeImage', 'Invoice', 'Receipt', 'SupportingDocument', 'NF2Form']);

            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type');
            $table->integer('file_size');

            $table->timestamp('upload_date')->useCurrent();
            $table->foreignId('uploaded_by')->constrained('users');

            $table->integer('version')->default(1);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
