<?php

namespace Aparlay\Core\Listeners;

use Aparlay\Core\Models\User;
use Aparlay\Payment\Models\Order;
use Aparlay\Payment\Models\Tip;

class UserStatsTipsUpdater
{
    public function handle($event)
    {
        $order = Order::findOrFail($event->orderId);

        if ($order->is_tip && $order->is_successful && !empty($order->entityObj->mediaObj)) {
            Tip::cacheByCreatorId($order->entityObj->creator['_id'], true);

            if (isset($order->entityObj->user['_id'])) {
                $receiver                                              = User::user($order->entityObj->user['_id'])->first();
                $receiverStats                                         = $receiver->stats;
                $receiverStats['amounts']['earned']['tips_commission'] = Tip::query()->user($order->entityObj->userObj->_id)->successful()->sum('commission_amount');
                $receiver->fill(['stats' => $receiverStats])->save();
            }

            if (isset($order->entityObj->referral['_id'])) {
                $receiver                                            = User::user($order->entityObj->referral['_id'])->first();
                $receiverStats                                       = $receiver->stats;
                $receiverStats['amounts']['earned']['tips_referral'] = Tip::query()->referral($order->entityObj->referralObj->_id)->successful()->sum('referral_commission_amount');
                $receiver->fill(['stats' => $receiverStats])->save();
            }

            if (isset($order->entityObj->creator['_id'])) {
                $sender                                  = User::user($order->entityObj->creator['_id'])->first();
                $senderStats                             = $sender->stats;
                $senderStats['amounts']['spent']['tips'] = Tip::query()->creator($order->entityObj->creatorObj->_id)->successful()->sum('amount');
                $sender->fill(['stats' => $senderStats])->save();
            }
        }
    }
}
