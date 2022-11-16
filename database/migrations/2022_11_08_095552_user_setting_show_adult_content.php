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
        $default = [
            'otp' => false,
            'show_adult_content' => false,
            'notifications' => [
                'unread_message_alerts' => false,
                'new_followers' => false,
                'news_and_updates' => false,
                'tips' => false,
                'new_subscribers' => false,
            ],
            'payment' => [
                'allow_unverified_cc' => false,
                'block_unverified_cc' => false,
                'block_cc_payments' => false,
                'unverified_cc_spent_amount' => 0,
            ],
            'block_unverified_cc' => false,
        ];
        foreach (User::lazy() as $user) {
            $setting = $user->setting;

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
        //
    }
};
