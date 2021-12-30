<?php

namespace Aparlay\Core\Database\Factories;

use Aparlay\Core\Models\Enums\UserDocumentStatus;
use Aparlay\Core\Models\Enums\UserDocumentType;
use Aparlay\Core\Models\UserDocument;
use Illuminate\Database\Eloquent\Factories\Factory;
use MongoDB\BSON\ObjectId;

class UserDocumentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserDocument::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'type' => $this->faker->randomElement([
                UserDocumentType::ID_CARD->value,
                UserDocumentType::SELFIE->value,
            ]),

            'status' => $this->faker->randomElement([
                UserDocumentStatus::CREATED->value,
                UserDocumentStatus::CONFIRMED->value,
            ]),
            'md5' => $this->faker->md5(),
            'file' => 'waptap.mp4',
            'files_history' => [],
            'mime_type' => 'video/mp4',
        ];
    }
}
