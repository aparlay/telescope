<?php

namespace Aparlay\Core\Events;

use Aparlay\Core\Api\V1\Resources\FollowResource;
use Aparlay\Core\Api\V1\Resources\MediaResource;
use Aparlay\Core\Api\V1\Resources\UserResource;
use Aparlay\Core\Models\Enums\UserNotificationCategory;
use Aparlay\Core\Models\UserNotification;
use Aparlay\Payment\Api\V1\Resources\TipResource;
use Illuminate\Broadcasting\InteractsWithBroadcasting;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MongoDB\BSON\ObjectId;

class SendMessageToSupportChatEvent
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(public string $userId, public string $message = '')
    {
    }
}
