<?php

namespace Aparlay\Core\Database\Factories;

use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\Enums\NoteCategory;
use Aparlay\Core\Models\Enums\NoteType;
use Aparlay\Core\Models\Note;
use Aparlay\Core\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use MongoDB\BSON\ObjectId;

class NoteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Note::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $creator = User::factory()->create();
        $user = User::factory()->create();
        $type = $this->faker->randomElement([
            NoteType::SUSPEND->value,
            NoteType::UNSUSPEND->value,
            NoteType::BAN->value,
            NoteType::UNBAN->value,
            NoteType::WARNING_MESSAGE->value,
            NoteType::BAN_ALL_CC_PAYMENT->value,
            NoteType::UNBAN_ALL_CC_PAYMENT->value,
        ]);
        $category = NoteCategory::LOG->value;

        return [
            'creator' => [
                '_id' => new ObjectId($creator['_id']),
                'username' => $creator['username'],
                'avatar' => $creator['avatar'],
            ],
            'user' => [
                '_id' => new ObjectId($user['_id']),
                'username' => $user['username'],
                'avatar' => $user['avatar'],
            ],
            'type' => $type,
            'category' => $category,
            'message' => $this->faker->sentence(5),
            'status' => $this->faker->numberBetween($min = 0, $max = 1),
            'created_by' => new ObjectId($creator['_id']),
            'updated_by' =>  new ObjectId($creator['_id']),
            'created_at' => DT::utcNow(),
            'updated_at' =>  DT::utcNow(),
        ];
    }
}
