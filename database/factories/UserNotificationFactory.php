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

        $user     = User::factory()->create();

        switch ($category) {
            case UserNotificationCategory::TIPS->value:
                $entityType = Tip::shortClassName();
                $entityId   = new ObjectId(Tip::factory()->create()->_id);

                break;
            case UserNotificationCategory::LIKES->value:
            case UserNotificationCategory::COMMENTS->value:
                $entityType = Media::shortClassName();
                $entityId   = new ObjectId(Media::factory()->create()->_id);

                break;
            case UserNotificationCategory::FOLLOWS->value:
                $entityType = Follow::shortClassName();
                $entityId   = new ObjectId(Follow::factory()->create()->_id);

                break;
            default:
                $entityType = User::shortClassName();
                $entityId   = new ObjectId($user->_id);

                break;
        }

        return [
            'user_id' => new ObjectId($user->_id),
            'entity._type' => $entityType,
            'entity._id' => $entityId,
            'message' => ($category == UserNotificationCategory::SYSTEM->value) ? 'Your Creator application has been approved! 🎉' : '',
            'category' => $this->faker->randomElement(UserNotificationCategory::getAllValues()),
            'status' => $this->faker->randomElement(UserNotificationStatus::getAllValues()),
            'created_by' => new ObjectId($user->_id),
            'updated_by' => new ObjectId($user->_id),
            'created_at' => DT::utcNow(),
            'updated_at' => DT::utcNow(),
        ];
    }
}
