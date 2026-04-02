<?php

namespace Database\Factories;

use App\Models\Bill;
use App\Models\procedureCode;
use Database\Factories\VisitsFactory;
use Database\Factories\ProcedureCodeFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Bill>
 */
class BillFactory extends Factory
{
    protected $model = Bill::class;

    public function definition(): array
    {
        $charges = fake()->randomFloat(2, 50, 5000);
        $insuranceCoverage = fake()->randomFloat(2, 0, $charges * 0.8);
        $discountAmount = fake()->randomFloat(2, 0, $charges * 0.1);
        $taxAmount = round(($charges - $discountAmount) * 0.05, 2);
        $billAmount = round($charges - $insuranceCoverage - $discountAmount + $taxAmount, 2);
        $paidAmount = fake()->randomFloat(2, 0, $billAmount);
        $outstandingAmount = round($billAmount - $paidAmount, 2);

        $status = match (true) {
            $outstandingAmount <= 0 => 'Paid',
            $paidAmount > 0 => 'Partial',
            default => fake()->randomElement(['Draft', 'Pending']),
        };

        $billDate = fake()->dateTimeBetween('-6 months', 'now');

        return [
            'visit_id' => VisitsFactory::new()->create()->id,
            'bill_number' => 'BILL-' . fake()->unique()->numerify('###############'),
            'bill_date' => $billDate->format('Y-m-d'),
            'procedure_code_id' =>  procedureCode::inRandomOrder()->first()->id ?? procedureCode::factory(),
            'charges' => $charges,
            'insurance_coverage' => $insuranceCoverage,
            'bill_amount' => $billAmount,
            'discount_amount' => $discountAmount,
            'tax_amount' => $taxAmount,
            'paid_amount' => $paidAmount,
            'outstanding_amount' => $outstandingAmount,
            'status' => $status,
            'generated_document_path' => null,
            'notes' => fake()->optional(0.4)->sentence(),
            'due_date' => fake()->dateTimeBetween($billDate, '+3 months')->format('Y-m-d'),
        ];
    }
}
