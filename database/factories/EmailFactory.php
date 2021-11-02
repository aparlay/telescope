<?php

namespace Aparlay\Core\Database\Factories;

use Aparlay\Core\Models\Email;
use Aparlay\Core\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use MongoDB\BSON\ObjectId;

class EmailFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Email::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user = User::factory()->create(['status' => 0, 'type' => 0]);

        return [
            'user' => [
                '_id' => new ObjectId($user->_id),
                'username' => $user->username,
                'avatar' => $user->avatar,
            ],
            'to' => $user->email,
            'status' => $user->status,
            'type' => $user->type,
        ];
    }
}
