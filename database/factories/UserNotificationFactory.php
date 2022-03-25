<?php

namespace Aparlay\Core\Database\Factories;

use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\Enums\UserNotificationCategory;
use Aparlay\Core\Models\Enums\UserNotificationStatus;
use Aparlay\Core\Models\Follow;
use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\User;
use Aparlay\Core\Models\UserNotification;
use Aparlay\Payment\Models\Tip;
use Illuminate\Database\Eloquent\Factories\Factory;
use MongoDB\BSON\ObjectId;

class UserNotificationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserNotification::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $category = $this->faker->randomElement(UserNotificationCategory::getAllValues());

        $usernotifiableType = match ($category) {
            UserNotificationCategory::TIPS->value => Tip::class,
            UserNotificationCategory::LIKES->value, UserNotificationCategory::COMMENTS->value => Media::class,
            UserNotificationCategory::FOLLOWS->value => Follow::class,
            UserNotificationCategory::SYSTEM->value => null
        };

        return [
            'user_id' => function () {
                return new ObjectId(User::factory()->create()->_id);
            },
            'entity._type' => $usernotifiableType,
            'entity._id' => function () use ($usernotifiableType) {
                return $usernotifiableType ? new ObjectId() : null;
            },
            'message' => ($category == UserNotificationCategory::SYSTEM->value) ? 'Your Creator application has been approved! 🎉' : null,
            'category' => $this->faker->randomElement(UserNotificationCategory::getAllValues()),
            'status' => $this->faker->randomElement(UserNotificationStatus::getAllValues()),
            'created_by' => function ($alert) {
                return new ObjectId($alert['user_id']);
            },
            'updated_by' => function ($alert) {
                return new ObjectId($alert['user_id']);
            },
            'created_at' => DT::utcNow(),
            'updated_at' => DT::utcNow(),
        ];
    }
}
