<?php

use Aparlay\Core\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (User::lazy() as $user) {
            /** @var User $user */
            $counters = [
                'followers' => $user->follower_count,
                'followings' => $user->following_count,
                'likes' => $user->like_count,
                'blocks' => $user->block_count,
                'followed_hashtags' => $user->followed_hashtag_count,
                'medias' => $user->media_count,
                'subscriptions' => 0,
                'subscribers' => 0,
                'chats' => 0,
                'notifications' => 0,
            ];

            $user->drop(['follower_count', 'following_count', 'like_count', 'block_count', 'followed_hashtag_count', 'media_count']);
            $stats = $user->stats;
            $stats['counters'] = $counters;
            $user->stats = $stats;
            $user->saveQuietly();
        }
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
};
