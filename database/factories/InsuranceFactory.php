<?php

namespace Database\Factories;

use App\Models\Insurance;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Insurance>
 */
class InsuranceFactory extends Factory
{
    protected $model = Insurance::class;

    public function definition(): array
    {
        $openedDate = fake()->dateTimeBetween('-2 years', 'now');
        $isActive = fake()->boolean(70);

        return [
            'name' => fake()->company() . ' Insurance',
            'email' => fake()->unique()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'status' => $isActive ? 'Active' : 'In-Active',
            'description' => fake()->paragraph(),
            'opened_date' => $openedDate->format('Y-m-d'),
            'closed_date' => !$isActive ? fake()->dateTimeBetween($openedDate, 'now')->format('Y-m-d') : null,
        ];
    }
}
