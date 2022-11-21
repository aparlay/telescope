<?php

use Aparlay\Core\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $default = [
            'otp' => false,
            'show_adult_content' => false,
            'notifications' => [
                'unread_message_alerts' => true,
                'news_and_updates' => true,
                'new_followers' => true,
                'new_subscribers' => true,
                'tips' => true,
                'likes' => true,
                'comments' => true,
            ],
            'payment' => [
                'allow_unverified_cc' => false,
                'block_unverified_cc' => false,
                'block_cc_payments' => false,
                'unverified_cc_spent_amount' => 0,
            ],
        ];
        foreach (User::lazy() as $user) {
            $user->setting = $default;
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
