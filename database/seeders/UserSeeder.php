<?php

namespace Database\Seeders;

use Database\Factories\UserFactory;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create 1 of each role for testing, then 6 random users
        UserFactory::new()->create(['name' => 'Admin User',   'email' => 'admin@bps.com',   'role' => 'Admin']);
        UserFactory::new()->create(['name' => 'Biller User',  'email' => 'biller@bps.com',  'role' => 'Biller']);
        UserFactory::new()->create(['name' => 'Poster User',  'email' => 'poster@bps.com',  'role' => 'Payment Poster']);
        UserFactory::new()->create(['name' => 'Doctor User',  'email' => 'doctor@bps.com',  'role' => 'Doctor']);

        UserFactory::new()->count(6)->create();
    }
}
