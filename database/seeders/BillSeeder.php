<?php

namespace Database\Seeders;

use App\Models\Visit;
use Database\Factories\BillFactory;
use Illuminate\Database\Seeder;

class BillSeeder extends Seeder
{
    public function run(): void
    {
        $visitIds = Visit::pluck('id')->toArray();

        foreach ($visitIds as $visitId) {
            BillFactory::new()->create([
                'visit_id' => $visitId,
            ]);
        }
    }
}
