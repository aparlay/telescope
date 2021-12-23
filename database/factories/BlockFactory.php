<?php

namespace Aparlay\Core\Database\Factories;

use Aparlay\Core\Models\Block;
use Aparlay\Core\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use MongoDB\BSON\ObjectId;

class BlockFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Block::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user' => function () {
                $user = User::factory()->create();

                return [
                    '_id' => new ObjectId($user->_id),
                    'username' => $user->username,
                    'avatar' => $user->avatar,
                ];
            },
            'creator' => function () {
                $user = User::factory()->create();

                return [
                    '_id' => new ObjectId($user->_id),
                    'username' => $user->username,
                    'avatar' => $user->avatar,
                ];
            },
            'is_deleted' => $this->faker->boolean(),
        ];
    }
}
