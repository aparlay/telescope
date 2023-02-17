<?php

namespace Aparlay\Core\Database\Factories;

use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\Enums\UserStatus;
use Aparlay\Core\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use MongoDB\BSON\ObjectId;

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
            'features' => array_fill_keys(array_keys(User::getFeatures()), false),
            'gender' => $this->faker->randomElement(array_keys(User::getGenders())),
            'type' => $this->faker->randomElement(array_keys(User::getTypes())),
            'status' => UserStatus::ACTIVE->value,
            'verification_status' => $this->faker->randomElement(array_keys(User::getVerificationStatuses())),
            'visibility' => $this->faker->randomElement(array_keys(User::getVisibilities())),
            'count_fields_updated_at' => [
                'followers' => DT::utcNow(),
                'followings' => DT::utcNow(),
                'blocks' => DT::utcNow(),
                'likes' => DT::utcNow(),
                'hashtags' => DT::utcNow(),
                'medias' => DT::utcNow(),
            ],
            'setting' => [
                'otp' => false,
                'show_adult_content' => 2,
                'filter_content_gender' => [
                    'female' => true,
                    'male' => false,
                    'transgender' => false,
                ],
                'notifications' => [
                    'unread_message_alerts' => true,
                    'news_and_updates' => true,
                    'new_followers' => true,
                    'new_subscribers' => true,
                    'tips' => true,
                    'likes' => true,
                    'comments' => true,
                ],
                'payment' => [
                    'allow_unverified_cc' => false,
                    'block_unverified_cc' => false,
                    'block_cc_payments' => false,
                    'unverified_cc_spent_amount' => 0,
                ],
            ],
            'subscriptions' => [],
            'user_agents' => [],
            'stats' => [
                'amounts' => [
                    'sent_tips' => 0,
                    'received_tips' => 0,
                    'subscriptions' => 0,
                    'subscribers' => 0,
                ],
                'counters' => [
                    'followers' => 0,
                    'followings' => 0,
                    'likes' => 0,
                    'blocks' => 0,
                    'followed_hashtags' => 0,
                    'medias' => 0,
                    'subscriptions' => 0,
                    'subscribers' => 0,
                    'chats' => 0,
                    'notifications' => 0,
                ],
            ],
            'blocks' => $this->simpleUser(),
            'likes' => $this->simpleUser(),
            'followers' => $this->simpleUser(),
            'followings' => $this->simpleUser(),
            'followed_hashtags' => [],
            'medias' => [],
            'promo_link' => null,
            'referral_id' => null,
            'country_alpha2' => 'US',
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

    protected function simpleUser($count = 5): array
    {
        $data = [];
        for ($i = 1; $i <= $count; $i++) {
            $data[] = [
                '_id' => new ObjectId(),
                'username' => $this->faker->userName(),
                'avatar' => $this->faker->imageUrl(),
            ];
        }

        return $data;
    }
}
