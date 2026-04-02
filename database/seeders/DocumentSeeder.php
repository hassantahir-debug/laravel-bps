<?php

namespace Database\Seeders;

use App\Models\Bill;
use App\Models\User;
use Database\Factories\DocumentFactory;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    public function run(): void
    {
        $billIds = Bill::pluck('id')->toArray();
        $userIds = User::pluck('id')->toArray();

        // Attach 1-3 documents to each bill
        foreach ($billIds as $billId) {
            $count = fake()->numberBetween(1, 3);
            DocumentFactory::new()->count($count)->create([
                'bill_id'     => $billId,
                'uploaded_by' => fake()->randomElement($userIds),
            ]);
        }
    }
}
