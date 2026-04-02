<?php

namespace Database\Seeders;

use App\Models\appointment;
use Database\Factories\VisitsFactory;
use Illuminate\Database\Seeder;

class VisitsSeeder extends Seeder
{
    public function run(): void
    {
        $appointmentIds = appointment::pluck('id')->toArray();

        foreach ($appointmentIds as $appointmentId) {
            VisitsFactory::new()->create([
                'appointment_id' => $appointmentId,
            ]);
        }
    }
}
