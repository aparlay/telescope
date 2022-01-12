<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Aparlay\Core\Models\User::where('stats', null)->chunk(200, function ($models) {
            foreach ($models as $user) {
                $user->stats = [
                    'amounts' => [
                        'sent_tips' => 0,
                        'received_tips' => 0,
                        'subscriptions' => 0,
                        'subscribers' => 0,
                    ],
                    'counters' => [
                        'followers' => $user->follower_count,
                        'followings' => $user->following_count,
                        'likes' => $user->like_count,
                        'blocks' => $user->block_count,
                        'followed_hashtags' => $user->followed_hashtag_count,
                        'medias' => $user->media_count,
                        'subscriptions' => 0,
                        'subscribers' => 0,
                    ],
                ];
                $user->save();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
