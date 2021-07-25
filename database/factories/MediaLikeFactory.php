<?php

namespace Aparlay\Core\Database\Factories;

use Aparlay\Core\Models\MediaLike;
use Aparlay\Core\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use MongoDB\BSON\ObjectId;

class MediaLikeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MediaLike::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'media_id' => function ($model) {
                return new ObjectId($model['media_id']);
            },
            'user_id' => function ($model) {
                return new ObjectId($model['user_id']);
            },
            'creator' => function ($model) {
                $user = User::user($model['user_id'])->first();
                return [
                    '_id' => new ObjectId($user->_id),
                    'username' => $user->username,
                    'avatar' => $user->avatar,
                ];
            },
            'created_by' => function ($model) {
                return new ObjectId($model['user_id']);
            },
            'updated_by' => function ($model) {
                return new ObjectId($model['user_id']);
            },
        ];
    }
}
