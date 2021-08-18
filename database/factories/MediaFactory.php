<?php

namespace Aparlay\Core\Database\Factories;

use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
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
            'file' => function () {
                $fileName = Str::random(10).'.mp4';
//                UploadedFile::fake()
//                    ->create($fileName, (string)(1024 * 1024 * rand(1, 1024)), 'video/mp4')
//                    ->storeAs('upload', $fileName);

                return $fileName;
            },
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
            'count_fields_updated_at' => [
                'followers' => DT::utcNow(),
                'followings' => DT::utcNow(),
                'blocks' => DT::utcNow(),
                'likes' => DT::utcNow(),
                'hashtags' => DT::utcNow(),
                'medias' => DT::utcNow(),
            ],
            'visibility' => $this->faker->randomElement(array_keys(Media::getVisibilities())),
            'status' => $this->faker->randomElement(array_keys(Media::getStatuses())),
            'is_music_licensed' => $this->faker->boolean(),
            'hashtags' => [],
            'people' => [],
            'processing_log' => [],
            'blocked_user_ids' => [],
            'creator' => function () {
                $user = User::factory()->create();

                return [
                    '_id' => new ObjectId($user->_id),
                    'username' => $user->username,
                    'avatar' => $user->avatar,
                ];
            },
            'user_id' => function (array $attributes) {
                return (string) $attributes['creator']['_id'];
            },
            'scores' => [],
            'sort_score' => $this->faker->randomNumber(4),
            'slug' => Str::random(6),
            'created_by' => function (array $attributes) {
                return $attributes['creator']['_id'];
            },
            'updated_by' => function (array $attributes) {
                return $attributes['creator']['_id'];
            },
        ];
    }
}
