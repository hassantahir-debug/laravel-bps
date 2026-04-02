<?php

namespace Database\Seeders;

use App\Models\patient;
use App\Models\User;
use Database\Factories\CasesFactory;
use Illuminate\Database\Seeder;

class CasesSeeder extends Seeder
{
    public function run(): void
    {
        // Use existing patients and doctors instead of creating new ones
        $patientIds = patient::pluck('id')->toArray();
        $doctorIds  = User::where('role', 'Doctor')->pluck('id')->toArray();

        CasesFactory::new()->count(15)->create([
            'patient_id'          => fake()->randomElement($patientIds),
            'referring_doctor_id' => fake()->randomElement($doctorIds),
        ]);
    }
}
