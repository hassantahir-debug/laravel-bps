<?php

namespace Database\Seeders;

use App\Models\Patient;
use Database\Factories\AccidentDetailsFactory;
use Illuminate\Database\Seeder;

class AccidentDetailsSeeder extends Seeder
{
    public function run(): void
    {
        $patients = Patient::all();

        if ($patients->isEmpty()) {
            AccidentDetailsFactory::new()->count(10)->create();
            return;
        }
        foreach ($patients as $patient) {
            AccidentDetailsFactory::new()->create([
                'patient_id' => $patient->id,
            ]);
        }
    }
}
