<?php

namespace Database\Factories;

use App\Models\Bill;
use App\Models\procedureCode;
use Database\Factories\VisitsFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Bill>
 */
class BillFactory extends Factory
{
    protected $model = Bill::class;

    public function definition(): array
    {
        $charges           = fake()->randomFloat(2, 50, 5000);
        $insuranceCoverage = fake()->randomFloat(2, 0, $charges * 0.8);
        $discountAmount    = fake()->randomFloat(2, 0, $charges * 0.1);
        $taxAmount         = round(($charges - $discountAmount) * 0.05, 2);
        $billAmount        = round($charges - $insuranceCoverage - $discountAmount + $taxAmount, 2);

        $statusType = fake()->randomElement([
            'Paid',
            'Paid',
            'Partial',
            'Partial',
            'Pending',
            'Draft',
            'Cancelled'
        ]);

        $paidAmount = match ($statusType) {
            'Paid'    => $billAmount,
            'Partial' => fake()->randomFloat(2, 0.01, $billAmount - 0.01),
            default   => 0
        };

        $outstandingAmount = round($billAmount - $paidAmount, 2);

        $allProcedures = procedureCode::all()->toArray();
        $billDate      = fake()->dateTimeBetween('-6 months', 'now');

        return [
            'visit_id'                => VisitsFactory::new()->create()->id,
            'bill_number'             => 'BILL-' . fake()->unique()->numberBetween(1, 99999),
            'bill_date'               => $billDate->format('Y-m-d'),
            'procedure_codes'         => fake()->randomElements(
                $allProcedures,
                fake()->numberBetween(1, min(3, count($allProcedures)))
            ),
            'charges'                 => $charges,
            'insurance_coverage'      => $insuranceCoverage,
            'bill_amount'             => $billAmount,
            'discount_amount'         => $discountAmount,
            'tax_amount'              => $taxAmount,
            'paid_amount'             => $paidAmount,
            'outstanding_amount'      => $outstandingAmount,
            'status'                  => $statusType,
            'generated_document_path' => null,
            'notes'                   => fake()->optional(0.4)->sentence(),
            'due_date'                => fake()->dateTimeBetween($billDate, '+3 months')->format('Y-m-d'),
        ];
    }
}
