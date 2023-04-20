<?php

namespace Aparlay\Core\Database\Seeders;

use Aparlay\Core\Models\Enums\UserNotificationCategory;
use Aparlay\Core\Models\Enums\UserNotificationStatus;
use Aparlay\Core\Models\User;
use Aparlay\Core\Models\UserNotification;
use Illuminate\Database\Seeder;
use MongoDB\BSON\ObjectId;

class UserNotificationSeeder extends Seeder
{
    protected $units = 20;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::factory()->create();

        UserNotification::factory()->count($this->units)
            ->state(function (array $attributes) use ($user) {
                return [
                    'creator' => [
                        '_id' => new ObjectId($user->_id),
                        'username' => $user->username,
                        'avatar' => $user->avatar,
                    ],
                    'category' => UserNotificationCategory::SYSTEM->value,
                    'status' => UserNotificationStatus::NOT_VISITED->value,
                    'message' => 'We have received your application and will review it shortly.',
                    'user_id' => new ObjectId($user->_id),
                    'entity._id' => new ObjectId($user->_id),
                    'entity._type' => 'User',
                ];
            })
            ->create();
    }
}
