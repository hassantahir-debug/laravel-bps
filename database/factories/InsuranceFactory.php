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
            'city' => fake()->city(),
            'status' => $isActive ? 'Active' : 'In-Active',
        ];
    }
}
