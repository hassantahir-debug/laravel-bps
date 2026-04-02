<?php

namespace Database\Seeders;

use Database\Factories\InsuranceFactory;
use Illuminate\Database\Seeder;

class InsuranceSeeder extends Seeder
{
    public function run(): void
    {
        InsuranceFactory::new()->count(10)->create();
    }
}
