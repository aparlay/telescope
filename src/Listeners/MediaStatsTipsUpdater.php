<?php

namespace Aparlay\Core\Listeners;

use Aparlay\Core\Models\Media;
use Aparlay\Payment\Models\Order;
use Aparlay\Payment\Models\Tip;

class MediaStatsTipsUpdater
{
    public function handle($event)
    {
        $order = Order::findOrFail($event->orderId);

        if ($order->is_tip && $order->is_successful && !empty($order->entityObj->media_id)) {
            $media = Media::query()->media($order->entityObj->media_id)->first();
            $media->fillAmountsField([
                'tips' => Tip::query()->media($order->entityObj->media_id)->successful()->sum('amount'),
            ]);
            $media->save();
        }
    }
}
