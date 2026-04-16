<?php

namespace Database\Factories;

use App\Models\appointment;
use Database\Factories\CasesFactory;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<appointment>
 */
class AppointmentFactory extends Factory
{
    protected $model = appointment::class;

    public function definition(): array
    {
        $doctor = \App\Models\User::where('role', 'Doctor')->inRandomOrder()->first() ?? UserFactory::new()->create(['role' => 'Doctor']);
        $caseId = \App\Models\cases::inRandomOrder()->value('id') ?? CasesFactory::new()->create()->id;

        return [
            'case_id' => $caseId,
            'appointment_type' => fake()->randomElement(['Initial', 'Follow-up', 'Consultation', 'Procedure', 'Emergency', 'Telehealth', 'Routine Checkup']),
            'appointment_status' => fake()->randomElement(['Scheduled', 'Confirmed', 'Checked In', 'In Progress', 'Completed', 'Cancelled', 'No Show', 'Rescheduled']),
            'appointment_date' => fake()->dateTimeBetween('-3 months', '+3 months')->format('Y-m-d'),
            'appointment_time' => fake()->time('H:i:s'),
            'duration_minutes' => fake()->randomElement([15, 30, 45, 60, 90]),
            'doctor_id' => $doctor->id,
            'doctor_name' => $doctor->name,
            'specialty_required' => fake()->optional(0.5)->randomElement([
                'General Medicine', 'Pediatrics', 'Cardiology', 'Orthopedics',
                'Dermatology', 'Neurology', 'Gynecology', 'Ophthalmology'
            ]),
            'notes' => fake()->optional(0.6)->sentence(),
            'reminder_sent' => fake()->boolean(40),
        ];
    }
}
