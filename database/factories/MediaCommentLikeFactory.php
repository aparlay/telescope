<?php

namespace Aparlay\Core\Database\Factories;

use Aparlay\Core\Models\MediaComment;
use Aparlay\Core\Models\MediaCommentLike;
use Aparlay\Core\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use MongoDB\BSON\ObjectId;

class MediaCommentLikeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MediaCommentLike::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'media_comment_id' => function (array $attributes) {
                return new ObjectId($attributes['media_comment_id']);
            },
            'creator' => function (array $attributes) {
                $user = User::user($attributes['user_id'])->first();

                return [
                    '_id' => new ObjectId($user->_id),
                    'username' => $user->username,
                    'avatar' => $user->avatar,
                ];
            },
            'created_by' => function (array $attributes) {
                return new ObjectId($attributes['user_id']);
            },
            'updated_by' => function (array $attributes) {
                return new ObjectId($attributes['user_id']);
            },
        ];
    }
}
