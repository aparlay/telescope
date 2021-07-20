<?php

namespace Aparlay\Core\Database\Factories;

use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use MongoDB\BSON\ObjectId;
use Str;

class MediaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Media::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'description' => $this->faker->sentence(5),
            'notes' => $this->faker->sentence(5),
            'location' => null,
            'hash' => $this->faker->sha1(),
            'file' => Str::random(10) . '.mp4',
            'files_history' => [],
            'mime_type' => 'video/mp4',
            'size' => $this->faker->randomDigitNotZero(),
            'length' => $this->faker->randomNumber(2),
            'length_watched' => $this->faker->randomNumber(6),
            'type' => 'video',
            'like_count' => $this->faker->randomDigitNotZero(),
            'likes' => [],
            'visit_count' => $this->faker->randomDigitNotZero(),
            'visits' => [],
            'comment_count' => $this->faker->randomDigitNotZero(),
            'comments' => [],
            'count_fields_updated_at' => DT::utcNow(),
            'visibility' => $this->faker->randomElement(array_keys(Media::getVisibilities())),
            'status' => $this->faker->randomElement(array_keys(Media::getStatuses())),
            'is_music_licensed' => $this->faker->boolean(),
            'hashtags' => [],
            'people' => [],
            'processing_log' => [],
            'blocked_user_ids' => [],
            'user_id' => function () {
                return User::factory()->create()->_id;
            },
            'creator' => function ($model) {
                $user = User::user($model['user_id'])->first();
                return [
                    '_id' => new ObjectId($user->_id),
                    'username' => $user->username,
                    'avatar' => $user->avatar,
                ];
            },
            'scores' => [],
            'sort_score' => $this->faker->randomNumber(4),
            'slug' => Str::random(6),
            'created_by' => function ($alert) {
                return new ObjectId($alert['user_id']);
            },
            'updated_by' => function ($alert) {
                return new ObjectId($alert['user_id']);
            }
        ];
    }
}
