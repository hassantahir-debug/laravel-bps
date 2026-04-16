<?php

namespace Database\Factories;

use App\Models\cases;
use Database\Factories\PatientFactory;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<cases>
 */
class CasesFactory extends Factory
{
    protected $model = cases::class;

    public function definition(): array
    {
        $patientId = \App\Models\patient::inRandomOrder()->value('id') ?? PatientFactory::new()->create()->id;
        $doctorId = \App\Models\User::inRandomOrder()->value('id') ?? UserFactory::new()->create()->id;
        $openedDate = fake()->dateTimeBetween('-1 year', 'now');
        $isClosed = fake()->boolean(30);

        return [
            'patient_id' => $patientId,
            'case_number' => 'CASE-' . fake()->unique()->numerify('######'),
            'case_type' => fake()->randomElement(['New', 'Follow-up', 'Emergency', 'Consultation', 'Surgical', 'Chronic']),
            'case_category' => fake()->randomElement([
                'General Medicine', 'Pediatrics', 'Cardiology', 'Orthopedics',
                'Dermatology', 'Neurology', 'Gynecology', 'Ophthalmology',
                'ENT', 'Dental', 'Psychiatry', 'Other'
            ]),
            'priority' => fake()->randomElement(['Low', 'Normal', 'High', 'Urgent']),
            'status' => fake()->randomElement(['Active', 'Closed', 'Transferred', 'On Hold']),
            'description' => fake()->paragraph(),
            'opened_date' => $openedDate->format('Y-m-d'),
            'is_accident' => fake()->boolean(10),
            'closed_date' => $isClosed ? fake()->dateTimeBetween($openedDate, 'now')->format('Y-m-d') : null,
            'referring_doctor_id' => $doctorId,
        ];
    }
}
