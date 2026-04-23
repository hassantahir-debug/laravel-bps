<?php

namespace Database\Factories;

use App\Models\Visit;
use Database\Factories\AppointmentFactory;
use Illuminate\Database\Eloquent\Factories\Factory;


class VisitFactory extends Factory
{
    protected $model = Visit::class;

    public function definition(): array
    {
        $appointmentId = \App\Models\Appointment::inRandomOrder()->value('id') ?? AppointmentFactory::new()->create()->id;
        $followUpRequired = fake()->boolean(30);

        return [
            'appointment_id' => $appointmentId,
            'visit_date' => fake()->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
            'visit_time' => fake()->time('H:i:s'),
            'diagnosis' => fake()->sentence(10),
            'treatment_notes' => fake()->paragraph(),
            'prescriptions' => fake()->optional(0.7)->paragraph(),
            'follow_up_required' => $followUpRequired,
            'follow_up_date' => $followUpRequired ? fake()->dateTimeBetween('now', '+3 months')->format('Y-m-d') : null,
            'status' => fake()->randomElement(['Pending', 'Completed', 'Cancelled']),
        ];
    }
}
