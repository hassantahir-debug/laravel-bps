<?php

namespace Database\Seeders;

use App\Models\appointment;
use App\Models\Visit;
use Database\Factories\VisitFactory;
use Illuminate\Database\Seeder;

class VisitsSeeder extends Seeder
{
    public function run(): void
    {
        $appointmentIds = appointment::pluck('id')->toArray();

        foreach ($appointmentIds as $appointmentId) {
            VisitFactory::new()->create([
                'appointment_id' => $appointmentId,
            ]);
        }
        Visit::factory(100)->create([
            'status' => 'Completed',
        ]);
    }
}
