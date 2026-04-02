<?php

namespace Database\Seeders;

use Database\Factories\PatientFactory;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        PatientFactory::new()->count(20)->create();
    }
}
