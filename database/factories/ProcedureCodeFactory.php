<?php

namespace Database\Factories;

use App\Models\ProcedureCode;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProcedureCode>
 */
class ProcedureCodeFactory extends Factory
{
    protected $model = ProcedureCode::class;

    public function definition(): array
    {
        $procedures = [
            '99201' => 'Office visit, new patient, minor',
            '99202' => 'Office visit, new patient, low complexity',
            '99203' => 'Office visit, new patient, moderate complexity',
            '99204' => 'Office visit, new patient, high complexity',
            '99211' => 'Office visit, established patient, minimal',
            '99212' => 'Office visit, established patient, straightforward',
            '99213' => 'Office visit, established patient, low complexity',
            '99214' => 'Office visit, established patient, moderate complexity',
            '99215' => 'Office visit, established patient, high complexity',
            '99281' => 'Emergency department visit, minor',
            '99282' => 'Emergency department visit, low complexity',
            '99283' => 'Emergency department visit, moderate complexity',
            '99284' => 'Emergency department visit, high complexity',
            '99285' => 'Emergency department visit, critical',
            '90834' => 'Psychotherapy, 45 minutes',
            '90837' => 'Psychotherapy, 60 minutes',
            '99391' => 'Preventive medicine, infant',
            '99392' => 'Preventive medicine, early childhood',
            '99393' => 'Preventive medicine, late childhood',
            '99394' => 'Preventive medicine, adolescent',
            '99395' => 'Preventive medicine, 18-39 years',
            '99396' => 'Preventive medicine, 40-64 years',
            '36415' => 'Routine venipuncture',
            '71046' => 'Chest X-ray, 2 views',
            '80053' => 'Comprehensive metabolic panel',
            '85025' => 'Complete blood count (CBC)',
            '87880' => 'Strep test, rapid',
            '93000' => 'Electrocardiogram (ECG)',
            '29881' => 'Knee arthroscopy with meniscectomy',
            '27447' => 'Total knee replacement',
        ];

        $code = fake()->unique()->randomElement(array_keys($procedures));

        return [
            'code' => $code,
            'description' => $procedures[$code],
            'price' => fake()->randomFloat(2, 50, 500),
        ];
    }
}