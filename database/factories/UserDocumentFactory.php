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
        $type = $this->faker->randomElement([
            UserDocumentType::ID_CARD->value,
            UserDocumentType::SELFIE->value,
        ]);

        $docPrefix = match ($type) {
            UserDocumentType::SELFIE->value => 'selfie_',
            UserDocumentType::ID_CARD->value => 'id_card_',
        };

        return [
            'type' => $type,
            'status' => $this->faker->randomElement([
                UserDocumentStatus::PENDING->value,
                UserDocumentStatus::APPROVED->value,
                UserDocumentStatus::REJECTED->value,
            ]),
            'md5' => $this->faker->md5(),
            'file' => $docPrefix.uniqid().'.jpg',
            'mime_type' => 'image/jpg',
        ];
    }
}
