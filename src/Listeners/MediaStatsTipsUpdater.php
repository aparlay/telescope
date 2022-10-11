<?php

namespace Aparlay\Core\Listeners;

use Aparlay\Chat\Api\V1\Dto\ChatDTO;
use Aparlay\Chat\Api\V1\Dto\MessageDTO;
use Aparlay\Chat\Api\V1\Services\ChatService;
use Aparlay\Chat\Api\V1\Services\MessageService;
use Aparlay\Chat\Models\Chat;
use Aparlay\Chat\Models\Enums\ChatCategory;
use Aparlay\Chat\Models\Enums\ChatStatus;
use Aparlay\Chat\Models\Enums\MessageType;
use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\User;
use Aparlay\Payment\Models\Enums\TipStatus;
use Aparlay\Payment\Models\Order;
use Aparlay\Payment\Models\Tip;
use Illuminate\Contracts\Queue\ShouldQueue;

class MediaStatsTipsUpdater
{
    public function handle($event)
    {
        $order = Order::findOrFail($event->orderId);

        if ($order->is_tip && $order->is_successful && ! empty($order->entityObj->media_id)) {
            $media = Media::query()->media($order->entityObj->media_id)->first();
            $media->fillAmountsField([
                'tips' => Tip::query()->media($order->entityObj->media_id)->successful()->sum('amount'),
            ]);
            $media->save();
        }
    }
}
