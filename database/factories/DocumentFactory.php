<?php

namespace Database\Factories;

use App\Models\document;
use Database\Factories\BillFactory;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<document>
 */
class DocumentFactory extends Factory
{
    protected $model = document::class;

    public function definition(): array
    {
        $documentType = fake()->randomElement(['Cheque Image', 'Invoice', 'Receipt', 'Supporting Document', 'NF2 Form']);
        $fileType = fake()->randomElement(['pdf', 'jpg', 'png', 'docx']);
        $fileName = fake()->slug(3) . '.' . $fileType;
        $billId = \App\Models\bill::inRandomOrder()->value('id') ?? BillFactory::new()->create()->id;
        $userId = \App\Models\User::inRandomOrder()->value('id') ?? UserFactory::new()->create()->id;

        return [
            'bill_id' => $billId,
            'document_type' => $documentType,
            'file_name' => $fileName,
            'file_path' => 'documents/' . $fileName,
            'file_type' => $fileType,
            'file_size' => fake()->numberBetween(1024, 10485760), // 1KB to 10MB
            'upload_date' => fake()->dateTimeBetween('-6 months', 'now'),
            'uploaded_by' => $userId,
            'version' => fake()->numberBetween(1, 5),
        ];
    }
}
