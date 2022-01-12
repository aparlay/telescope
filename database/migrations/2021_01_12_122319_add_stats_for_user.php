<?php

use Aparlay\Core\Models\User;
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
                ],
            ];

        foreach ($users as $user) {
            if (! isset($user->stats)) {
                $user->fill(['stats' => $stats])->save();
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
