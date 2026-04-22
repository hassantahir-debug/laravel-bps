<?php

namespace Database\Seeders;

use App\Models\Cases;
use App\Models\User;
use Database\Factories\AppointmentFactory;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        $caseIds   = Cases::pluck('id')->toArray();
        $doctorIds = User::where('role', 'Doctor')->pluck('id')->toArray();

        foreach ($caseIds as $caseId) {
            $doctor = User::find(fake()->randomElement($doctorIds));

            AppointmentFactory::new()->create([
                'case_id'     => $caseId,
                'doctor_id'   => $doctor->id,
                'doctor_name' => $doctor->name,
            ]);
        }
    }
}
