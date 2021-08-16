<?php

namespace Aparlay\Core\Database\Factories;

use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'username' => $this->faker->unique()->userName(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified' => $this->faker->boolean(),
            'email_verified_at' => DT::utcNow(),
            'phone_number' => $this->faker->unique()->e164PhoneNumber(),
            'phone_number_verified' => $this->faker->boolean(),
            'remember_token' => Str::random(10),
            'bio' => $this->faker->sentence(30),
            'full_name' => $this->faker->name(),
            'avatar' => $this->faker->imageUrl(),
            'auth_key' => $this->faker->randomKey(),
            'password_hash' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
            'password_reset_token' => $this->faker->randomKey(),
            'setting',
            'features' => array_fill_keys(array_keys(User::getFeatures()), false),
            'gender' => $this->faker->randomElement(array_keys(User::getGenders())),
            'interested_in' => $this->faker->randomElement(array_keys(User::getInterestedIns())),
            'type' => $this->faker->randomElement(array_keys(User::getTypes())),
            'status' => $this->faker->randomElement(array_keys(User::getStatuses())),
            'visibility' => $this->faker->randomElement(array_keys(User::getVisibilities())),
            'follower_count' => $this->faker->randomDigit(),
            'following_count' => $this->faker->randomDigit(),
            'block_count' => $this->faker->randomDigit(),
            'followed_hashtag_count' => $this->faker->randomDigit(),
            'like_count' => $this->faker->randomDigit(),
            'media_count' => $this->faker->randomDigit(),
            'count_fields_updated_at' => [
                'followers' => DT::utcNow(),
                'followings' => DT::utcNow(),
                'blocks' => DT::utcNow(),
                'likes' => DT::utcNow(),
                'hashtags' => DT::utcNow(),
                'medias' => DT::utcNow(),
            ],
            'setting' => [
                'otp' => $this->faker->boolean(),
            ],
            'blocks' => [],
            'likes' => [],
            'followers' => [],
            'followings' => [],
            'followed_hashtags' => [],
            'medias' => [],
            'promo_link' => null,
            'referral_id' => null,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
