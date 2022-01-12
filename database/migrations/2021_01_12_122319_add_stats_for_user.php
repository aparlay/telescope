<?php

use Aparlay\Core\Models\User;
use Aparlay\Payment\Models\Tip;
use Illuminate\Database\Migrations\Migration;
use MongoDB\BSON\ObjectId;

class AddStatsForUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       $users = User::get();

       $stats = [
                'amounts' => [
                    'sent_tips' => 0,
                    'received_tips' => 0,
                    'active_subscriptions' => 0,
                    'active_subscribers' => 0,
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
                ]
            ];

       foreach($users as $user) {
            if(!isset($user->stats)) {
                $user->fill(['stats' => $stats])->save();
                $tip = Tip::first();
                dd($tip);
                $receiver = User::findOrFail('61d422180549fd0413380b36');
                $receiverStats = $receiver->stats;
                $totalReceiveTips = isset($receiverStats['amount']) && isset($receiverStats['amount']['received_tips']) ? $receiverStats['amount']['received_tips'] + $tip->amount : $tip->amount; 
                $receiverStats['amount']['received_tips'] = $totalReceiveTips;
                $receiver->fill(['stats' => $receiverStats])->save();

                $sender = User::findOrFail('61d422180549fd0413380b36');
                $senderStats = $sender->stats;
                $totalSendTips = isset($senderStats['amount']) && isset($senderStats['amount']['sent_tips']) ? $senderStats['amount']['sent_tips'] + $tip->amount : $tip->amount;
                $senderStats['amount']['sent_tips'] = $totalReceiveTips;
                $sender->fill(['stats' => $senderStats])->save();
            }
       }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
