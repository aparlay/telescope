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
            $setting = $user->setting;
            $setting['notifications'] = [
                'unread_message_alerts' => true,
                'news_and_updates' => true,
                'new_followers' => true,
                'new_subscribers' => true,
                'tips' => true,
                'likes' => true,
                'comments' => true,
            ];

            $user->setting = $setting;
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
