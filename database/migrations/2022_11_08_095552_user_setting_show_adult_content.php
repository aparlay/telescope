<?php

use Aparlay\Core\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class() extends Migration {
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
            $setting       = $user->setting;

            if (empty($setting)) {
                $setting = $default;
            }

            if (empty($user->setting['show_adult_content'])) {
                $setting['show_adult_content'] = false;
            }

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

    }
};
