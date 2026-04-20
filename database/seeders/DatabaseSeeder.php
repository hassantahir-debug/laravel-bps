<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // No foreign key dependencies
            UserSeeder::class,
            InsuranceSeeder::class,
            ProcedureCodeSeeder::class,
            PatientSeeder::class,
            AccidentDetailsSeeder::class,

            // Depends on Patient + User
            CasesSeeder::class,

            // Depends on Cases + User
            AppointmentSeeder::class,

            // Depends on Appointments
            VisitsSeeder::class,

            // Depends on Visits + ProcedureCodes
        ]);
    }
}
