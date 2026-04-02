<?php

namespace Database\Seeders;

use App\Models\Bill;
use App\Models\User;
use Database\Factories\PaymentFactory;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $billIds  = Bill::pluck('id')->toArray();
        $userIds  = User::pluck('id')->toArray();

        // Create 1-2 payments per bill
        foreach ($billIds as $billId) {
            $count = fake()->numberBetween(1, 2);
            for ($i = 0; $i < $count; $i++) {
                PaymentFactory::new()->create([
                    'bill_id'     => $billId,
                    'received_by' => fake()->randomElement($userIds),
                ]);
            }
        }
    }
}
