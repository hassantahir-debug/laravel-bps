<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->unique()->constrained('visits')->onDelete('cascade');
            $table->string('bill_number')->unique();
            $table->date('bill_date');
            $table->foreignId('procedure_code_id')->nullable()->constrained('procedures_codes')->onDelete('set null');

            $table->decimal('charges', 10, 2);
            $table->decimal('insurance_coverage', 10, 2)->default(0);
            $table->decimal('bill_amount', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('outstanding_amount', 10, 2);

            $table->enum('status', ['Draft', 'Pending', 'Partial', 'Paid', 'Cancelled', 'Written Off'])->default('Pending');
            $table->string('generated_document_path')->nullable();
            $table->text('notes')->nullable();
            $table->date('due_date')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
