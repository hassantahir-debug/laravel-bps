<?php

namespace Database\Seeders;

use App\Models\visits;
use App\Models\procedureCode;
use Database\Factories\BillFactory;
use Illuminate\Database\Seeder;

class BillSeeder extends Seeder
{
    public function run(): void
    {
        $visitIds        = visits::pluck('id')->toArray();
        $procedureCodeIds = procedureCode::pluck('id')->toArray();

        foreach ($visitIds as $visitId) {
            BillFactory::new()->create([
                'visit_id'          => $visitId,
                'procedure_code_id' => fake()->randomElement($procedureCodeIds),
            ]);
        }
    }
}
