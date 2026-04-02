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
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->unique()->constrained('visits')->onDelete('cascade');
            $table->string('bill_number')->unique();
            $table->date('bill_date');
            $table->json('procedures_codes')->nullable();

            $table->decimal('charges', 10, 2);
            $table->decimal('insurance_coverage', 10, 2)->default(0);
            $table->decimal('bill_amount', 10, 2); // Formula: charges - insurance - discount + tax [cite: 74, 122, 420]
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('paid_amount', 10, 2)->default(0); // Cumulative total [cite: 78, 135]
            $table->decimal('outstanding_amount', 10, 2); // Formula: bill_amount - paid_amount [cite: 77, 129, 420]


            // Status Transitions: Pending -> Partial -> Paid [cite: 237, 423]
            $table->enum('status', ['Draft', 'Pending', 'Partial', 'Paid', 'Cancelled', 'Written Off'])->default('Pending');
            $table->string('generated_document_path')->nullable(); // NF2 Form PDF path [cite: 80, 128]
            $table->date('due_date')->nullable();
            
            $table->timestamps();
            $table->softDeletes(); // Audit trail safety [cite: 152, 268]
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
