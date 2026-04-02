<?php

namespace Database\Factories;

use App\Models\Payment;
use Database\Factories\BillFactory;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        $paymentMode = fake()->randomElement([
            'Cash', 'Check', 'Bank Transfer', 'Credit Card',
            'Debit Card', 'Insurance', 'Online Payment'
        ]);

        return [
            'bill_id' => BillFactory::new()->create()->id,
            'payment_number' => 'PAY-' . fake()->unique()->numerify('######'),
            'amount_paid' => fake()->randomFloat(2, 10, 2000),
            'payment_mode' => $paymentMode,
            'check_number' => $paymentMode === 'Check' ? fake()->numerify('####-####') : null,
            'bank_name' => in_array($paymentMode, ['Check', 'Bank Transfer']) ? fake()->company() . ' Bank' : null,
            'transaction_reference' => in_array($paymentMode, ['Bank Transfer', 'Credit Card', 'Debit Card', 'Online Payment'])
                ? strtoupper(fake()->bothify('TXN-####-????-####'))
                : null,
            'payment_date' => fake()->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
            'payment_status' => fake()->randomElement(['Completed', 'Pending', 'Failed', 'Refunded']),
            'cheque_file_path' => null,
            'notes' => fake()->optional(0.3)->sentence(),
            'received_by' => UserFactory::new()->create()->id,
        ];
    }
}
