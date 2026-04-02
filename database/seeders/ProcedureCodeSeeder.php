<?php

namespace Database\Seeders;

use Database\Factories\ProcedureCodeFactory;
use Illuminate\Database\Seeder;

class ProcedureCodeSeeder extends Seeder
{
    public function run(): void
    {
        // Seed all 30 predefined CPT codes from the factory
        ProcedureCodeFactory::new()->count(30)->create();
    }
}
