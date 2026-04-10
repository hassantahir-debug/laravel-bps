<?php

namespace Database\Factories;

use App\Models\accidentDetails;
use App\Models\patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<accidentDetails>
 */
class AccidentDetailsFactory extends Factory
{
    protected $model = accidentDetails::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'patient_id' => patient::factory(),
            'date_of_accident' => fake()->dateTimeBetween('-1 years', 'now')->format('Y-m-d'),
            'place_of_accident' => fake()->streetAddress(),
            'time_of_accident' => fake()->dateTimeBetween('-1 years', 'now')->format('Y-m-d H:i:s'),
            'veichle_no' => fake()->bothify('??-####'),
            'veichle_type' => fake()->randomElement(['Car', 'Motorcycle', 'Truck', 'SUV', 'Van']),
            'veichle_model' => fake()->randomElement(['Corolla', 'Civic', 'F-150', 'Altima', 'Model S', 'CX-5']),
            'accident_description' => fake()->paragraph(),
            'injury_description' => fake()->sentence(),
            'insurance_names' => fake()->company(),
            'insurance_policy_no' => fake()->bothify('POL-#####'),
        ];
    }
}
